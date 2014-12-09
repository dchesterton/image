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
     * Test that JPEG class returns an Xmp object when there is XMP data.
     *
     * @covers ::getXmp
     */
    public function testGetXmp()
    {
        $jpeg = JPEG::fromFile(__DIR__ . '/../Images/Xmp/meta.JPG');

        $xmp = $jpeg->getXmp();

        $this->assertInstanceOf(Xmp::class, $xmp);
        $this->assertEquals('A headline here', $xmp->getHeadline());
    }

    /**
     * Test that JPEG class returns an empty XMP object when there is no XMP data.
     *
     * @covers ::getXmp
     */
    public function testGetXmpNoMeta()
    {
        $jpeg = JPEG::fromFile(__DIR__ . '/../Images/Xmp/nometa.JPG');

        $xmp = $jpeg->getXmp();

        $this->assertInstanceOf(Xmp::class, $xmp);
        $this->assertEquals('', $xmp->getHeadline());
    }
}
