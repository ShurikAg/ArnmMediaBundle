<?php
namespace Arnm\MediaBundle\Controller;

use Arnm\CoreBundle\Controllers\ArnmController;
use Symfony\Component\HttpFoundation\Request;
use Arnm\MediaBundle\Service\MediaManager;
use Arnm\MediaBundle\Entity\Media;
use Imagine\Gd\Imagine;
use Imagine\Image\ImageInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * This controller is responsible for rendering (redirecting to) media resource
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class MediaRenderController extends ArnmController
{

    const SIZE_ORIGINAL = 'original';

    /**
     * Redirects to the correct URL for requested resource
     *
     * @param string $size
     * @param string $file
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function imageAction($size, $file)
    {
        // find media by file name
        $media = $this->getMediaManager()->findMediaByFile($file);

        if (! ($media instanceof Media)) {
            throw $this->createNotFoundException('Media resource not found!');
        }

        // build the full file name structure and find desired dimensions
        $filePath = $file;
        $width = null;
        $height = null;
        $mode = ImageInterface::THUMBNAIL_INSET;
        if ($size != self::SIZE_ORIGINAL) {
            $sizeArray = explode('.', $size);
            if (count($sizeArray) == 1) {
                $width = $size;
            } elseif (count($sizeArray) == 2) {
                $width = $sizeArray[0];
                $height = $sizeArray[1];
            } elseif (count($sizeArray) == 3) {
                $width = $sizeArray[0];
                $height = $sizeArray[1];
                $mode = $sizeArray[2];
            }

            if (empty($width)) {
                $width = null;
            }
            if (empty($height)) {
                $height = null;
            }

            $filePath = 'cache/' . str_replace(array('.','/'), array('_','_'), $file) . '/' . ((empty($width)) ? 'null' : $width) . '_' . ((empty($height)) ? 'null' : $height) . '_' . $mode . '/' . $file;
        }

        $signedUrl = null;
        try {
            // get signed url
            $signedUrl = $this->getMediaManager()->getObjectPublicUrl($filePath);
        } catch (\InvalidArgumentException $e) {
            // if it is the original image then something is wrong
            // since we have the record but not the resource that we can sign
            if ($size == self::SIZE_ORIGINAL) {
                throw $this->createNotFoundException("Media source was not found!");
            }
        }

        if (empty($signedUrl)) {
            // we probably still didn't create the cache for this size
            // let's resize first
            $tmpFile = sys_get_temp_dir() . '/' . str_replace('/', '_', $file);
            $extention = pathinfo($file, PATHINFO_EXTENSION);
            $this->getMediaManager()
                ->getStorage()
                ->getObject($file, $tmpFile);

            $imagine = new Imagine();
            $originalImage = $imagine->open($tmpFile);

            $imageTransformer = $this->get('arnm_media.image_transformer');
            // create new temp file for resied image
            $resizedFile = tempnam(sys_get_temp_dir(), 'tumb') . '.' . $extention;
            $resizedImage = $imageTransformer->createThumbnail($originalImage, array($width, $height), $mode);

            $resizedImage->save($resizedFile);

            // save new file into storage
            $this->getMediaManager()
                ->getStorage()
                ->saveObject($filePath, $resizedFile);
            // get signed URL
            $signedUrl = $this->getMediaManager()->getObjectPublicUrl($filePath);
        }

        return $this->redirect($signedUrl);
    }

    /**
     * Gets the file direct download link
     *
     * @param $file
     *
     * @return Response
     */
    public function fileAction($file)
    {
        $media = $this->getMediaManager()->findMediaByFile($file);

        if (! ($media instanceof Media)) {
            throw $this->createNotFoundException('Media resource not found!');
        }

        try {
            // get signed url
            $signedUrl = $this->getMediaManager()->getObjectPublicUrl($file);

            return $this->redirect($signedUrl);

        } catch (\InvalidArgumentException $e) {
            // if it is the original image then something is wrong
            // since we have the record but not the resource that we can sign
            throw $this->createNotFoundException("Media source was not found!");
        }
    }

    /**
     * Gets media manager object
     *
     * @return MediaManager
     */
    protected function getMediaManager()
    {
        return $this->get('arnm_media.manager');
    }
}

