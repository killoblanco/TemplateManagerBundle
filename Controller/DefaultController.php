<?php

namespace killoblanco\TemplateManagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class DefaultController
 * @package killoblanco\TemplateManagerBundle\Controller
 * @Route("/template-manager", name="tm")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="tm_index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine();

        $templates = $em->getRepository('TemplateManagerBundle:Template')
            ->findAll();

        $parameters = [
            'page_title' => 'List',
            'templates' => $templates,
        ];

        return $this->render('@TemplateManager/pages/templates/list.html.twig', $parameters);
    }

    /**
     * @Route("/components", name="tm_components")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function componentsAction()
    {
        $parameters = [
            'page_title' => 'Components',
            'uno' => [
                'value' => 'hola mundo',
            ],
        ];
        return $this->render('@TemplateManager/pages/components.html.twig', $parameters);
    }
}
