<?php

namespace killoblanco\TemplateManagerBundle\Controller;

use killoblanco\TemplateManagerBundle\Entity\Language;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TemplateLanguageController
 * @package killoblanco\TemplateManagerBundle\Controller
 * @Route("/template-manager", name="tm")
 */
class TemplateLanguageController extends Controller
{
    /**
     * @Route("/languages", name="tm_language_index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine();
        $language = new Language();
        $language_form = $this->createFormBuilder($language)
            ->add('name', TextType::class, [
                'label' => 'Name:'
            ])
            ->add('value', TextType::class, [
                'label' => 'Value:'
            ])
            ->add('id', HiddenType::class)
            ->getForm();
        $language_form->handleRequest($request);
        if ($request->getMethod() == "POST") {
            if ($request->get('id')) {
                $language = $em->getRepository('TemplateManagerBundle:Language')->find($request->get('id'));
                $language->setName($request->get('name'));
                $language->setValue($request->get('value'));
                $language->setModified(new \DateTime());
            } else {
                $language = new Language();
                $language->setName($request->get('name'));
                $language->setValue($request->get('value'));
                $language->setModified(new \DateTime());
            }
            $em->getManager()->persist($language);
            $em->getManager()->flush();
            $languages = $em->getRepository('TemplateManagerBundle:Language')
                ->findAll();
            return JsonResponse::create($this->get('serializer')->normalize($languages, 'json'));
        };
        $languages = $em->getRepository('TemplateManagerBundle:Language')
            ->findAll();
        $parameters = [
            'page_title' => 'Languages',
            'languages' => json_encode($this->get('serializer')->normalize($languages, 'json')),
            'language_form' => $language_form->createView(),
        ];

        return $this->render('@TemplateManager/pages/languages.html.twig', $parameters);
    }

    /**
     * @Route("/language/edit/status", name="tm_change_lang_status")
     * @param Request $request
     * @return mixed
     */
    public function changeTypeStatusAction(Request $request)
    {
        $em = $this->getDoctrine();
        $language = $em->getRepository('TemplateManagerBundle:Language')
            ->find($request->get('id'));
        if ($request->get('active') == "true") {
            $language->setActive(false);
        } else {
            $language->setActive(true);
        }
        $em->getManager()->persist($language);
        $em->getManager()->flush();
        $languages = $em->getRepository('TemplateManagerBundle:Language')
            ->findAll();
        return JsonResponse::create($this->get('serializer')->normalize($languages, 'json'));
    }
}
