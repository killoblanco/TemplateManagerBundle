<?php

namespace killoblanco\TemplateManagerBundle\Controller;

use killoblanco\TemplateManagerBundle\Entity\Type;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Type controller.
 *
 * @Route("template-manager/type")
 */
class TypeController extends Controller
{

    /**
     * Lists all type entities.
     *
     * @Route("/", name="tm_type_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $types = $em->getRepository('TemplateManagerBundle:Type')->findAll();

        return $this->render('@TemplateManager/pages/type/index.html.twig', [
            'types' => $types,
        ]);
    }


    /**
     * Creates a new type entity.
     *
     * @Route("/new", name="tm_type_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $type = new Type();
        $form = $this->createForm('killoblanco\TemplateManagerBundle\Form\TypeType', $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $type->setModified(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($type);
            $em->flush($type);

            return $this->redirectToRoute('tm_type_show', [ 'id' => $type->getId() ]);
        }

        return $this->render('@TemplateManager/pages/type/new.html.twig', [
            'type' => $type,
            'form' => $form->createView(),
        ]);
    }


    /**
     * Finds and displays a type entity.
     *
     * @Route("/{id}", name="tm_type_show")
     * @Method("GET")
     */
    public function showAction(Type $type)
    {
        $deleteForm = $this->createDeleteForm($type);

        return $this->render('@TemplateManager/pages/type/show.html.twig', [
            'type'        => $type,
            'delete_form' => $deleteForm->createView(),
        ]);
    }


    /**
     * Displays a form to edit an existing type entity.
     *
     * @Route("/{id}/edit", name="tm_type_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Type $type)
    {
        $deleteForm = $this->createDeleteForm($type);
        $editForm = $this->createForm('killoblanco\TemplateManagerBundle\Form\TypeType', $type);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tm_type_show', [ 'id' => $type->getId() ]);
        }

        return $this->render('@TemplateManager/pages/type/edit.html.twig', [
            'type'        => $type,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }


    /**
     * Deletes a type entity.
     *
     * @Route("/{id}", name="tm_type_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Type $type)
    {
        $form = $this->createDeleteForm($type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($type);
            $em->flush($type);
        }

        return $this->redirectToRoute('tm_type_index');
    }


    /**
     * Creates a form to delete a type entity.
     *
     * @param Type $type The type entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Type $type)
    {
        return $this->createFormBuilder()->setAction($this->generateUrl('tm_type_delete',
                [ 'id' => $type->getId() ]))->setMethod('DELETE')->getForm();
    }
}
