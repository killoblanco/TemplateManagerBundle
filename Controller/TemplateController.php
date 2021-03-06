<?php

namespace killoblanco\TemplateManagerBundle\Controller;

use killoblanco\TemplateManagerBundle\Entity\Template;
use killoblanco\TemplateManagerBundle\Entity\TemplateDefaults;
use killoblanco\TemplateManagerBundle\Form\TemplatesType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TemplateController
 * @package killoblanco\TemplateManagerBundle\Controller
 * @Route("/template-manager/templates", name="tm")
 */
class TemplateController extends Controller
{

    /**
     * @Route("/list", name="tm_templates_list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine();

        $templates = $em->getRepository('TemplateManagerBundle:Template')->findBy([ 'active' => true ]);

        $parameters = [
            'page_title' => 'List',
            'templates'  => $templates,
        ];

        return $this->render('@TemplateManager/pages/templates/list.html.twig', $parameters);
    }


    /**
     * @Route("/edit/{id}", name="tm_templates_edit")
     * @param Request $request
     * @param         $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine();

        $template = $em->getRepository('TemplateManagerBundle:Template')->find($id);

        $parameters = [
            'page_title' => 'Edit',
            'template'   => $template,
        ];

        return $this->render('@TemplateManager/pages/templates/edit.html.twig', $parameters);
    }


    /**
     * @Route("/settings/{id}", name="tm_templates_settings", defaults={"id"= null})
     * @param Request $request
     * @param         $id
     *
     * @return Response
     */
    public function settingsAction(Request $request, $id)
    {
        $em = $this->getDoctrine();

        if ($id) {
            $template = $em->getRepository(Template::class)->find($id);
            if ($template->getThumbnail() !== "" && $template->getThumbnail() !== null) {
                $template->setThumbnail(new File('uploads/template-manager/thumbnails/'.$template->getThumbnail()));
            }
        } else {
            $template = new Template();
        }

        $form = $this->createForm(TemplatesType::class, $template);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $template = $form->getData();

            if ($template->getThumbnail()) {
                $thumbnail = $template->getThumbnail();
                $thumbnailName = md5(uniqid()).'.'.$thumbnail->guessExtension();

                $thumbnail->move($this->getParameter('thumbnails_directory'), $thumbnailName);
                $template->setThumbnail(new File('uploads/template-manager/thumbnails/'.$thumbnailName));
            }

            $em->getManager()->persist($template);
            $em->getManager()->flush();

            return $this->redirectToRoute('tm_index');

        }

        $parameters = [
            'form'     => $form->createView(),
            'template' => $template
        ];

        return $this->render('@TemplateManager/pages/templates/new.html.twig', $parameters);
    }


    /**
     * @Route("/save/{id}", name="tm_templates_save")
     * @param Request $request
     * @param         $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function saveAction(Request $request, $id = null)
    {
        $em = $this->getDoctrine();

        $template = $em->getRepository('TemplateManagerBundle:Template')->find($id);

        $controls = $request->request->all();
        unset($controls['html']);
        $controls = json_encode($controls);

        $defaults = $em->getRepository(TemplateDefaults::class)->findOneBy([ 'template' => $template ]);

        if ($defaults) {
            $defaults->setData($controls);
        } else {
            $defaults = new TemplateDefaults();
            $defaults->setTemplate($template);
            $defaults->setData($controls);
        }

        $em->getManager()->persist($template);
        $em->getManager()->persist($defaults);
        $em->getManager()->flush();

        return $this->redirectToRoute('tm_index');
    }


    /**
     * @param Template $template
     *
     * @return Response
     */
    public function previewTemplate(Template $template)
    {
        $defaults = $this->getDoctrine()->getRepository(TemplateDefaults::class)->find($template->getId());

        $response = "";

        if ($defaults) {
            $defaults = json_decode($defaults);
            $i = 0;
            foreach ($defaults as $name => $value) {
                $regex = "([{]{2}[\s\w('\w]+[, ']+".$name."+[')\s]+[}]{2})m";
                if ($i == 0) {
                    $response = preg_replace($regex, $value, $template->getBase());
                } else {
                    $response = preg_replace($regex, $value, $response);
                }
            }
        }

        // todo: create behavior when template does not have default values

        return new Response($response);
    }
}
