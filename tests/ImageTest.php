<?php

namespace CSD\Image\Tests;

use CSD\Image\Image;
use CSD\Image\Format\PNG;
use CSD\Image\Format\JPEG;

/**
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 *
 * @coversDefaultClass \CSD\Image
 */
class ImageTest extends \PHPUnit\Framework\TestCase
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
