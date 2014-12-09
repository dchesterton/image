<?php

namespace CSD\Photo\Tests\Image;

use CSD\Photo\Image\PNG;
use CSD\Photo\Metadata\Xmp;

/**
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 *
 * @coversDefaultClass \CSD\Photo\Image\PNG
 */
class PNGTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that JPEG class returns an Xmp object when there is XMP data.
     *
     * @covers ::getXmp
     */
    public function testGetXmp()
    {
        $jpeg = PNG::fromFile(__DIR__ . '/../Fixtures/xmp.png');



    }
}
