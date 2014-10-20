<?php
namespace Arnm\MediaBundle\Controller;

use Arnm\CoreBundle\Controllers\ArnmController;
use Symfony\Component\HttpFoundation\Request;
use Arnm\MediaBundle\Service\MediaManager;
use Arnm\MediaBundle\Entity\Media;
use Imagine\Gd\Imagine;
use Imagine\Image\ImageInterface;

/**
 * This controller is responsible for rendering (redirecting to) media resource
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class MediaRenderController extends ArnmController
{
    const FILTER_ORIGINAL = 'original';

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
        //find media by file name
        $media = $this->getMediaManager()->findMediaByFile($file);

        if (!($media instanceof Media)) {
            throw $this->createNotFoundException('Media resource not found!');
        }

        //build the full file name structure
        $filePath = $file;
        if ($size != self::FILTER_ORIGINAL) {
            $filePath = 'cache/'.str_replace(array('.', '/'), array('_','_'), $file).'/'.$width.'_'.$height.'_'.$mode.'/'.$file;
        }

        $signedUrl = null;
        try {
            //get signed url
            $signedUrl = $this->getMediaManager()->getObjectPublicUrl($filePath);
        } catch (\InvalidArgumentException $e){
            //if it is the original image then something is wrong
            //since we have the record but not the resource that we can sign
            if($size == self::FILTER_ORIGINAL) {
                throw $this->createNotFoundException("Media source was not found!");
            }
        }

        if (empty($signedUrl)) {
            //we probably still didn't create the cache for this size
            //let's resize first
            $tmpFile = sys_get_temp_dir().'/'.$file;
            $extention = pathinfo($tmpFile, PATHINFO_EXTENSION);
            $this->getMediaManager()->getStorage()->getObject($file, $tmpFile);

            //create filter config
            $filterOptions = array(
                    "size" => array($width, $height),
                    "mode" => $mode,
                    "allow_upscale" => true
            );

            $imagine = new Imagine();
            $originalImage = $imagine->open($tmpFile);


            $thumbFilterLoader = $this->get('liip_imagine.filter.loader.thumbnail');
            //create new temp file for resied image
            $resizedFile = tempnam(sys_get_temp_dir(), 'tumb').'.'.$extention;
            $resizedImage = $thumbFilterLoader->load($originalImage, $filterOptions);

            $resizedImage->save($resizedFile);

            //save new file into storage
            $this->getMediaManager()->getStorage()->saveObject($filePath, $resizedFile);
            //get signed URL
            $signedUrl = $this->getMediaManager()->getObjectPublicUrl($filePath);
        }


        return $this->redirect($signedUrl);
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

