<?php
namespace Arnm\MediaBundle\Tests\Services\Graphics;

use Arnm\MediaBundle\Service\Graphics\ImageTransformer;
use Liip\ImagineBundle\Imagine\Filter\Loader\ThumbnailFilterLoader;
use Imagine\Gd\Imagine;
use Imagine\Image\ImageInterface;

/**
 * ImageTransformer test case.
 */
class ImageTransformerTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @var ImageTransformer
     */
    private $imageTransformer;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $thumbFilterLoader = new ThumbnailFilterLoader();

        $this->imageTransformer = new ImageTransformer($thumbFilterLoader);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated ImageTransformerTest::tearDown()
        $this->imageTransformer = null;

        parent::tearDown();
    }

    /**
     * Tests ImageTransformer->createThumbnail()
     */
    public function testCreateThumbnail()
    {
        $testFile = realpath(dirname(__FILE__) . '/Sign.png');
        $testTarget = dirname(__FILE__) . '/result.png';

        // load an image first
        $imagine = new Imagine();
        $origin = $imagine->open($testFile);

        $this->assertEquals(107, $origin->getSize()->getWidth());
        $this->assertEquals(48, $origin->getSize()->getHeight());

        //start testing the resize functionality
        $resized = $this->imageTransformer->createThumbnail($origin, array(100,100));
        $this->assertEquals(100, $resized->getSize()->getWidth());
        $this->assertEquals(45, $resized->getSize()->getHeight());
    }
}
