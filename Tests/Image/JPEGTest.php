<?php

namespace CSD\Photo\Tests\Image;

use CSD\Photo\Image\JPEG;
use CSD\Photo\Metadata\Xmp;

/**
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 *
 * @coversDefaultClass \CSD\Photo\Image\JPEG
 */
class JPEGTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that JPEG can read XMP embedded with Photo Mechanic.
     */
    public function testGetXmpPhotoMechanic()
    {
        $jpeg = JPEG::fromFile(__DIR__ . '/../Fixtures/metapm.JPG');

        $xmp = $jpeg->getXmp();

        $this->assertInstanceOf(Xmp::class, $xmp);
        $this->assertSame('Headline', $xmp->getHeadline());
    }

    /**
     * Test that JPEG can read XMP embedded with Photoshop.
     */
    public function testGetXmpPhotoshop()
    {
        $jpeg = JPEG::fromFile(__DIR__ . '/../Fixtures/metaphotoshop.JPG');

        $xmp = $jpeg->getXmp();

        $this->assertInstanceOf(Xmp::class, $xmp);
        $this->assertSame('Headline', $xmp->getHeadline());
    }

    /**
     * Test that JPEG class returns an empty XMP object when there is no XMP data.
     */
    public function testGetXmpNoMeta()
    {
        $jpeg = JPEG::fromFile(__DIR__ . '/../Fixtures/nometa.JPG');

        $xmp = $jpeg->getXmp();

        $this->assertInstanceOf(Xmp::class, $xmp);
        $this->assertNull($xmp->getHeadline());
    }
}
