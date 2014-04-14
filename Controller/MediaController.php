<?php
namespace Arnm\MediaBundle\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Arnm\CoreBundle\Controllers\ArnmController;

use Arnm\MediaBundle\Form\MediaType;
use Symfony\Component\HttpFoundation\Response;
use Arnm\MediaBundle\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
/**
 * Main media controller
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class MediaController extends ArnmController
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('ArnmMediaBundle:Media')->findAll();

        return $this->render('ArnmMediaBundle:Media:index.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * Shows a from for updating existing media resource
     *
     * @return Response
     */
    public function newAction()
    {
        $media = new Media();
        $media->setWebDir($this->getWebDir());
        $form = $this->createForm(new MediaType(), $media);

        if ($this->getRequest()->getMethod() === 'POST') {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($media);
                $em->flush();

                $this->getSession()
                    ->getFlashBag()
                    ->add('notice', $this->get('translator')
                    ->trans('media.message.create.success', array(), 'media'));

                return $this->redirect($this->generateUrl('arnm_media'));
            }
        }
        return $this->render('ArnmMediaBundle:Media:new.html.twig', array(
            'media' => $media,
            'form' => $form->createView()
        ));
    }

    /**
     * Shows a from for updating existing media resource
     *
     * @param int $id
     *
     * @return Response
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $media = $em->getRepository('ArnmMediaBundle:Media')->findOneById($id);
        $media->setWebDir($this->getWebDir());
        $form = $this->createForm(new MediaType(), $media);

        if ($this->getRequest()->getMethod() === 'POST') {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                if($media->media instanceof UploadedFile){
                    $media->setUpdated(new \DateTime());
                }

                $em->persist($media);
                $em->flush();

                $this->getSession()
                    ->getFlashBag()
                    ->add('notice', $this->get('translator')
                    ->trans('media.message.update.success', array(), 'media'));

                return $this->redirect($this->generateUrl('arnm_media_edit', array('id' => $media->getId())));
            }
        }
        return $this->render('ArnmMediaBundle:Media:edit.html.twig', array(
            'media' => $media,
            'form' => $form->createView()
        ));
    }

    /**
     * Deletes Media object
     *
     * @param int $id
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $media = $em->getRepository('ArnmMediaBundle:Media')->findOneById($id);

        if (! $media) {
            throw $this->createNotFoundException('Unable to find Media entity.');
        }

        $media->setWebDir($this->getWebDir());

        foreach ($media->getAttributes() as $attribtue) {
            $em->remove($attribtue);
        }
        $em->remove($media);
        $em->flush();

        $this->getSession()
            ->getFlashBag()
            ->add('notice', $this->get('translator')
            ->trans('media.message.delete.success', array(), 'media'));

        return $this->redirect($this->generateUrl('arnm_media'));
    }
}
