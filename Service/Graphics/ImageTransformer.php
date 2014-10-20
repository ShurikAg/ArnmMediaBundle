<?php
namespace Arnm\MediaBundle\Service\Graphics;

use Liip\ImagineBundle\Imagine\Filter\Loader\ThumbnailFilterLoader;
use Imagine\Image\ImageInterface;
/**
 * This class is simply srapping the functionality of image transformation
 * for ease of use and testability
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class ImageTransformer
{

    /**
     * @var ThumbnailFilterLoader
     */
    private $thumbnailFilterLoader;

    /**
     * Constructor
     *
     * @param ThumbnailFilterLoader $thumbFilterLoader
     */
    public function __construct(ThumbnailFilterLoader $thumbFilterLoader)
    {
        $this->setThumbnailFilterLoader($thumbFilterLoader);
    }

    /**
     * Creates a thumbnail for a given image with a specific size
     *
     * @param ImageInterface $image
     * @param array          $size
     * @param string         $mode
     *
     * @return ImageInterface
     */
    public function createThumbnail(ImageInterface $image, array $size)
    {
        $options = array(
            'size' => $size,
            'mode' => ImageInterface::THUMBNAIL_INSET
        );

        return $this->getThumbnailFilterLoader()->load($image, $options);
    }

	/**
     * @return ThumbnailFilterLoader
     */
    public function getThumbnailFilterLoader()
    {
        return $this->thumbnailFilterLoader;
    }

	/**
     * @param \Liip\ImagineBundle\Imagine\Filter\Loader\ThumbnailFilterLoader $thumbnailFilterLoader
     */
    public function setThumbnailFilterLoader(ThumbnailFilterLoader $thumbnailFilterLoader)
    {
        $this->thumbnailFilterLoader = $thumbnailFilterLoader;
    }
}