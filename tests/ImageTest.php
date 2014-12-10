<?php

namespace CSD\Photo\Tests;

use CSD\Photo\Image;
use CSD\Photo\Image\PNG;
use CSD\Photo\Image\JPEG;

/**
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 *
 * @coversDefaultClass \CSD\Photo\Image
 */
class ImageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::fromFile
     */
    public function testPNG()
    {
        $image = Image::fromFile(__DIR__ . '/Fixtures/nometa.png');
        $this->assertInstanceOf(PNG::class, $image);
    }

    /**
     * @covers ::fromFile
     */
    public function testJPG()
    {
        $image = Image::fromFile(__DIR__ . '/Fixtures/nometa.jpg');
        $this->assertInstanceOf(JPEG::class, $image);
    }

    /**
     * @covers ::fromFile
     */
    public function testUppercase()
    {
        $image = Image::fromFile(__DIR__ . '/Fixtures/UPPERCASE.JPG');
        $this->assertInstanceOf(JPEG::class, $image);
    }

    /**
     * @covers ::fromFile
     */
    public function testJPEG()
    {
        $image = Image::fromFile(__DIR__ . '/Fixtures/nometa.jpeg');
        $this->assertInstanceOf(JPEG::class, $image);
    }

    /**
     * @covers ::fromFile
     * @expectedException \Exception
     * @expectedExceptionMessage Unrecognised file name
     */
    public function testInvalidFile()
    {
        Image::fromFile(__FILE__);
    }
}
