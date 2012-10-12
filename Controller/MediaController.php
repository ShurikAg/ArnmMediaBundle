<?php
namespace Arnm\MediaBundle\Controller;

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
        $em = $this->getDoctrine()->getEntityManager();
        $entities = $em->getRepository('ArnmMediaBundle:Media')->findAll();

        return $this->render('ArnmMediaBundle:Media:index.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * Shows a from for uploading new media resource
     *
     * @return Response
     */
    public function newAction()
    {
        $media = new Media();
        $form = $this->createForm(new MediaType(), $media);

        if($this->getRequest()->getMethod() === 'POST') {
            $form->bindRequest($this->getRequest());
            if($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();

                $em->persist($media);
                $em->flush();

                return $this->redirect($this->generateUrl('arnm_media'));
            }
        }
        return $this->render('ArnmMediaBundle:Media:new.html.twig', array(
            'media' => $media,
            'form' => $form->createView()
        ));
    }

    public function deleteAction($id)
    {
        if($id) {
            $em = $this->getDoctrine()->getEntityManager();
            $media = $em->getRepository('ArnmMediaBundle:Media')->findOneById($id);

            if(! $media) {
                throw $this->createNotFoundException('Unable to find Media entity.');
            }

            $em->remove($media);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('arnm_media'));
    }
}
