<?php

namespace killoblanco\TemplateManagerBundle\Controller;

use killoblanco\TemplateManagerBundle\Entity\Type;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class TypeController
 * @package killoblanco\TemplateManagerBundle\Controller
 * @Route("/template-manager", name="tm")
 */
class TypeController extends Controller
{
    /**
     * @Route("/type", name="tm_type")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function typeAction(Request $request)
    {
        $em = $this->getDoctrine();
        $type = new Type();
        $type_form = $this->createFormBuilder($type)
            ->add('name', TextType::class, [
                'label' => 'Type:'
            ])
            ->add('id', HiddenType::class)
            ->getForm();
        $type_form->handleRequest($request);
        if ($request->getMethod() == "POST") {
            if ($request->get('id')) {
                $type = $em->getRepository('TemplateManagerBundle:Type')->find($request->get('id'));
                $type->setName($request->get('name'));
                $type->setModified(new \DateTime());
            } else {
                $type = new Type();
                $type->setName($request->get('name'));
                $type->setModified(new \DateTime());
            }
            $em->getManager()->persist($type);
            $em->getManager()->flush();
            $types = $em->getRepository('TemplateManagerBundle:Type')
                ->findAll();
            return JsonResponse::create($this->get('serializer')->normalize($types, 'json'));
        };
        $types = $em->getRepository('TemplateManagerBundle:Type')
            ->findAll();
        $parameters = [
            'page_title' => 'Types',
            'types' => json_encode($this->get('serializer')->normalize($types, 'json')),
            'types_form' => $type_form->createView(),
        ];
        return $this->render('@TemplateManager/pages/type.html.twig', $parameters);
    }

    /**
     * @Route("/type/status/change", name="tm_change_type_status")
     * @param Request $request
     * @return mixed
     */
    public function changeTypeStatusAction(Request $request)
    {
        $em = $this->getDoctrine();
        $type = $em->getRepository('TemplateManagerBundle:Type')
            ->find($request->get('id'));
        if ($request->get('active') == "true") {
            $type->setActive(false);
        } else {
            $type->setActive(true);
        }
        $em->getManager()->persist($type);
        $em->getManager()->flush();
        $types = $em->getRepository('TemplateManagerBundle:Type')
            ->findAll();
        return JsonResponse::create($this->get('serializer')->normalize($types, 'json'));
    }
}
