<?php

namespace Urbant\CConvertBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Urbant\CConvertBundle\Controller\BaseAdminController;
use Urbant\CConvertBundle\Entity\User;
use Urbant\CConvertBundle\Form\UserType;

/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends BaseAdminController
{
    
    protected $pageCatId = 'user';
    
    /**
     * Lists all User entities.
     */
    public function indexAction()
    {
        $this->pageId = 'list';
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('UrbantCConvertBundle:User')->findAll();

        $vars = array(
            'users' => $entities,
        );
        return $this->render('UrbantCConvertBundle:User:index.html.twig', $vars);
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="user_new")
     * @Template()
     */
    public function newAction()
    {
        $this->pageId = 'add';

        $entity = new User();
        $form   = $this->createForm(new UserType(), $entity);

        $vars = array(
            'user' => $entity,
            'form'   => $form->createView(),
        );
        return $this->render('UrbantCConvertBundle:User:new.html.twig', $vars);
    }

    /**
     * Creates a new User entity.
     */
    public function createAction()
    {
        $entity  = new User();
        $request = $this->getRequest();
        $form    = $this->createForm(new UserType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->get('session')->setFlash('user.add.message', 'ユーザーを登録しました。');
            return $this->redirect($this->generateUrl('UrbantCConvertBundle_user_add', array('id' => $entity->getId())));
        }

        $vars = array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
        return $this->render('UrbantCConvertBundle:User:new.html.twig', $vars);
    }

    /**
     * Displays a form to edit an existing User entity.
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('UrbantCConvertBundle:User')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('指定されたユーザーIDは存在しません。ID: ' . $id);
        }

        $editForm = $this->createForm(new UserType(), $entity);

        $vars = array(
            'user'      => $entity,
            'form'   => $editForm->createView(),
        );
        return $this->render('UrbantCConvertBundle:User:edit.html.twig', $vars);
    }

    /**
     * Edits an existing User entity.
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UrbantCConvertBundle:User')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('指定されたIDのユーザーは存在しません。 ID: ' . $id);
        }

        $editForm   = $this->createForm(new UserType(), $entity);
        $request = $this->getRequest();
        $editForm->bindRequest($request);
        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            $this->get('session')->setFlash('user.edit.message', 'ユーザー情報を更新しました。');
            return $this->redirect($this->generateUrl('UrbantCConvertBundle_user_edit', array('id' => $id)));
        }

        $vars = array(
            'user'      => $entity,
            'form'   => $editForm->createView(),
        );
        return $this->render('UrbantCConvertBundle:User:edit.html.twig', $vars);
    }

    /**
     * Deletes a User entity.
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('UrbantCConvertBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('user'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
