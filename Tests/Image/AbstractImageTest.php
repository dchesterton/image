<?php

namespace CSD\Photo\Tests\Image;

use CSD\Photo\Image\AbstractImage;
use CSD\Photo\Metadata\Aggregate;
use CSD\Photo\Metadata\Exif;
use CSD\Photo\Metadata\Iptc;
use CSD\Photo\Metadata\UnsupportedException;
use CSD\Photo\Metadata\Xmp;
use Mockery as M;

/**
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 *
 * @coversDefaultClass \CSD\Photo\Image\AbstractImage
 */
class AbstractImageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::getAggregateMeta
     */
    public function testGetAggregate()
    {
        $image = $this->getMockForAbstractClass(AbstractImage::class);
        $image->expects($this->once())->method('getXmp')->will($this->returnValue(m::mock(Xmp::class)));
        $image->expects($this->once())->method('getIptc')->will($this->returnValue(m::mock(Iptc::class)));
        $image->expects($this->once())->method('getExif')->will($this->returnValue(m::mock(Exif::class)));

        $aggregate = $image->getAggregateMeta();

        $this->assertInstanceOf(Aggregate::class, $aggregate);
    }

    /**
     * @covers ::getAggregateMeta
     */
    public function testGetAggregateWithUnsupportedTypes()
    {
        $image = $this->getMockForAbstractClass(AbstractImage::class);
        $image->expects($this->once())->method('getXmp')->will($this->throwException(new UnsupportedException));
        $image->expects($this->once())->method('getIptc')->will($this->throwException(new UnsupportedException));
        $image->expects($this->once())->method('getExif')->will($this->throwException(new UnsupportedException));

        $aggregate = $image->getAggregateMeta();

        $this->assertInstanceOf(Aggregate::class, $aggregate);
    }

    /**
     * @covers ::save
     * @covers ::setFilename
     */
    public function testSave()
    {
        $tmp = tempnam(sys_get_temp_dir(), 'PNG');

        $image = $this->getMockForAbstractClass(AbstractImage::class);
        $image->expects($this->once())->method('getBytes')->will($this->returnValue('Test'));

        $return = $image->setFilename($tmp);

        $this->assertSame($image, $return);

        $image->save();

        $this->assertEquals('Test', file_get_contents($tmp));
    }

    /**
     * @covers ::save
     */
    public function testSaveWithFilename()
    {
        $tmp = tempnam(sys_get_temp_dir(), 'PNG');

        $image = $this->getMockForAbstractClass(AbstractImage::class);
        $image->expects($this->once())->method('getBytes')->will($this->returnValue('Test'));

        $image->save($tmp);

        $this->assertEquals('Test', file_get_contents($tmp));
    }

    /**
     * @covers ::save
     * @expectedException \Exception
     * @expectedExceptionMessage Must provide a filename
     */
    public function testSaveWithNoFilename()
    {
        $image = $this->getMockForAbstractClass(AbstractImage::class);
        $image->save();
    }
}
