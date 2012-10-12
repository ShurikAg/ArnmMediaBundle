<?php
namespace Arnm\MediaBundle\Tests\Entity;

use Arnm\MediaBundle\Entity\Attribute;

use Doctrine\Common\Collections\ArrayCollection;

use Arnm\MediaBundle\Entity\Media;
/**
 * Media test case.
 */
class MediaTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Arnm\MediaBundle\Entity\Media
     */
    private $media;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->media = new Media();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->media = null;
        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct()
    {
        // TODO Auto-generated constructor
    }

    /**
     * Tests Media->getId()
     */
    public function testGetId()
    {
        $this->assertNull($this->media->getId());
    }

    /**
     * Tests Media->setName()
     */
    public function testSetGetName()
    {
        $this->media->setName('name');
        $this->assertEquals('name', $this->media->getName());
    }

    /**
     * Tests Media->setFile()
     */
    public function testSetGetFile()
    {
        $this->media->setFile('file');
        $this->assertEquals('file', $this->media->getFile());
    }

    /**
     * Tests Media->setSize()
     */
    public function testSetGetSize()
    {
        $this->media->setSize(987987);
        $this->assertEquals(987987, $this->media->getSize());
    }

    /**
     * Tests Media->getMedia()
     */
    public function testSetGetMedia()
    {
        $this->assertNull($this->media->getMedia());
    }

    /**
     * Tests Media->setTag()
     */
    public function testSetGetTag()
    {
        $this->media->setTag('tag');
        $this->assertEquals('tag', $this->media->getTag());
    }

    /**
     * Tests Media->getAttributes()
     */
    public function testAddGetAttributes()
    {
        $attrs = $this->media->getAttributes();
        $this->assertTrue($attrs instanceof ArrayCollection);
        $this->assertEquals(0, $attrs->count());
        $this->media->addAttribute(new Attribute());
        $attrs = $this->media->getAttributes();
        $this->assertTrue($attrs instanceof ArrayCollection);
        $this->assertEquals(1, $attrs->count());
        try {
            $this->media->addAttribute('string');
        } catch (\Exception $e) {
        }
    }
}

