<?php

namespace killoblanco\TemplateManagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/asdf")
     */
    public function indexAction()
    {
        return $this->render('@TemplateManager/pages/index.html.twig');
    }
}
