<?php

namespace killoblanco\TemplateManagerBundle\Controller;

use killoblanco\TemplateManagerBundle\Entity\TemplateDefaults;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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

        $templates = $em->getRepository('TemplateManagerBundle:Template')
            ->findBy(['active' => true]);

        $parameters = [
            'page_title' => 'List',
            'templates' => $templates,
        ];

        return $this->render('@TemplateManager/pages/templates/list.html.twig', $parameters);
    }

    /**
     * @Route("/edit/{id}", name="tm_templates_edit")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine();

        $template = $em->getRepository('TemplateManagerBundle:Template')
            ->find($id);

        $parameters = [
            'page_title' => 'Edit',
            'template' => $template,
        ];

        return $this->render('@TemplateManager/pages/templates/edit.html.twig', $parameters);
    }

    /**
     * @Route("/save/{id}", name="tm_templates_save")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function saveAction(Request $request, $id = null)
    {
        $em = $this->getDoctrine();

        $template = $em->getRepository('TemplateManagerBundle:Template')
            ->find($id);

        $template->setHtml($request->get('html'));

        $controls = $request->request->all();
        unset($controls['html']);
        $controls = json_encode($controls);

        $defaults = $em->getRepository(TemplateDefaults::class)
            ->findOneBy(['template' => $template]);

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

        return $this->redirectToRoute('tm_templates_list');
    }
}
