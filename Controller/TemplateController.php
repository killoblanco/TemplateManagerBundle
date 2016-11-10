<?php

namespace killoblanco\TemplateManagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

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
}
