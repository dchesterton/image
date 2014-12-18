<?php

namespace CSD\Image\Tests\Type;

use CSD\Image\Type\WebP;
use CSD\Image\Metadata\Exif;
use CSD\Image\Metadata\Xmp;

/**
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 *
 * @coversDefaultClass \CSD\Image\Type\WebP
 */
class WebPTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that a non-WebP file throws an exception.
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Invalid WebP file
     *
     * @covers ::fromFile
     * @covers ::__construct
     */
    public function testFromFileInvalidWebP()
    {
        WebP::fromFile(__DIR__ . '/../Fixtures/nometa.jpg');
    }

    public function testFromFile()
    {
        $webp = WebP::fromFile(__DIR__ . '/../Fixtures/meta.webp');
        $this->assertInstanceOf(WebP::class, $webp);

        $xmp = $webp->getXmp();

        $this->assertInstanceOf(XMP::class, $xmp);
        $this->assertSame('Headline', $xmp->getHeadline());
    }

    public function testChangeXmp()
    {
        $tmp = tempnam(sys_get_temp_dir(), 'WebP');

        $webp = WebP::fromFile(__DIR__ . '/../Fixtures/meta.webp');
        $webp->getXmp()->setHeadline('PHP headline');
        $webp->save($tmp);

        $newWebp = WebP::fromFile($tmp);

        $this->assertSame('PHP headline', $newWebp->getXmp()->getHeadline());
    }

    public function testGetExif()
    {
        $webp = WebP::fromFile(__DIR__ . '/../Fixtures/exif.webp');
        $exif = $webp->getExif();

        $this->assertInstanceOf(Exif::class, $exif);

        // todo: test actual value of exif
    }

    /**
     * @expectedException \CSD\Image\Metadata\UnsupportedException
     * @expectedExceptionMessage WebP files do not support IPTC metadata
     *
     * @covers ::getIptc
     */
    public function testGetIptc()
    {
        $webp = WebP::fromFile(__DIR__ . '/../Fixtures/meta.webp');
        $webp->getIptc();
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Only extended WebP format is supported
     */
    public function testSimpleUnsupported()
    {
        WebP::fromFile(__DIR__ . '/../Fixtures/simple.webp');
    }
}
