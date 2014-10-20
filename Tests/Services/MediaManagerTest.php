<?php
namespace Arnm\MediaBundle\Tests\Services;


use Arnm\MediaBundle\Service\MediaManager;
/**
 * MediaManager test case.
 */
class MediaManagerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Tests MediaManager->__construct()
	 */
	public function test__construct()
	{
	    $storage = $this->getMock('Arnm\MediaBundle\Service\Storage\S3Storage', array('stub'), array(), '', false);
	    $cacheProvider = $this->getMock('Doctrine\Common\Cache\MemcacheCache', array('stub'), array(), '', false);
		$doctrine = $this->getMock('Doctrine\Bundle\DoctrineBundle\Registry', array('stub'), array(), '', false);

	    $mgr = new MediaManager($storage, $cacheProvider, $doctrine);

		$this->assertTrue($mgr instanceof MediaManager);
	}

	/**
	 * Tests getObjectPublicUrl method
	 *
	 * @depends test__construct
	 */
	public function testGetObjectPublicUrl()
	{
	    $storage = $this->getMock('Arnm\MediaBundle\Service\Storage\S3Storage', array('getObjectUrl', 'objectExists'), array(), '', false);
	    $storage->expects($this->once())
                 ->method('getObjectUrl')
                 ->with($this->equalTo('key'))
	             ->will($this->returnValue('http://domain.com/object/key'));
	    $storage->expects($this->once())
                 ->method('objectExists')
                 ->with($this->equalTo('key'))
	             ->will($this->returnValue(true));
	    $cacheProvider = $this->getMock('Doctrine\Common\Cache\MemcacheCache', array('contains', 'save'), array(), '', false);
	    $cacheProvider->expects($this->once())
        	    ->method('contains')
        	    ->with($this->equalTo('key'))
        	    ->will($this->returnValue(false));
	    $cacheProvider->expects($this->once())
        	    ->method('save')
        	    ->with($this->equalTo('key'), $this->equalTo('http://domain.com/object/key'), $this->equalTo(MediaManager::SIGNED_URL_EXPIRATION-MediaManager::SIGNED_URL_EXPIRATION_THRESHOLD))
        	    ->will($this->returnValue(false));

	    $doctrine = $this->getMock('Doctrine\Bundle\DoctrineBundle\Registry', array('stub'), array(), '', false);

	    $mgr = new MediaManager($storage, $cacheProvider, $doctrine);
	    $mgr->getObjectPublicUrl('key');
	}
}

