<?php
namespace CSD\Photo\Tests\Metadata;

use CSD\Photo\Image\JPEG;
use CSD\Photo\Metadata\Iptc;
use CSD\Photo\Metadata\Aggregate;
use CSD\Photo\Metadata\Xmp;

/**
 * Unit tests for {@see \CSD\Photo\Metadata\Aggregate}.
 *
 * @coversDefaultClass \CSD\Photo\Metadata\Aggregate
 */
class AggregateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function getXmpAndIptcFields()
    {
        return [
            ['headline'],
            ['caption'],
            ['location'],
            ['city'],
            ['state'],
            ['country'],
            ['countryCode'],
            ['photographerName'],
            ['credit'],
            ['photographerTitle'],
            ['source'],
            ['copyright'],
            ['objectName'],
            ['captionWriters'],
            ['instructions'],
            ['category'],
            ['supplementalCategories'],
            ['transmissionReference'],
            ['urgency'],
            ['keywords']
        ];
    }

    /**
     * Test the meta fields which only have a value for XMP and IPTC, which is majority.
     *
     * @dataProvider getXmpAndIptcFields
     */
    public function testXmpIptcField($field)
    {
        $method = 'get' . ucfirst($field);

        $xmp = $this->getMock(Xmp::class);
        $iptc = $this->getMock(Iptc::class);

        $xmp->expects($this->any())
            ->method($method)
            ->will($this->returnValue('XMP value'));

        $iptc->expects($this->any())
            ->method($method)
            ->will($this->returnValue('IPTC value'));

        $aggregateReader = new Aggregate($xmp, $iptc);

        $this->assertEquals('XMP value', $aggregateReader->$method());

        // change priority so IPTC is first
        $aggregateReader->setPriority(['iptc', 'xmp']);

        $this->assertEquals('IPTC value', $aggregateReader->$method());

        // change priority so nothing should be returned
        $aggregateReader->setPriority([]);

        $this->assertEquals(null, $aggregateReader->$method());
    }

    /**
     * @dataProvider getXmpAndIptcFields
     */
    public function testXmpIptcFallThrough($field)
    {
        $method = 'get' . ucfirst($field);

        $xmp = $this->getMock(Xmp::class);
        $iptc = $this->getMock(Iptc::class);

        $xmp->expects($this->any())
            ->method($method)
            ->will($this->returnValue(null));

        $iptc->expects($this->any())
            ->method($method)
            ->will($this->returnValue('IPTC value'));

        $aggregateReader = new Aggregate($xmp, $iptc);

        // should always be IPTC as XMP returns null
        $this->assertEquals('IPTC value', $aggregateReader->$method());
    }

    /**
     * Test that all fields return null if no providers are set.
     */
    public function testNullWhenNoProviders()
    {
        $reader = new Aggregate;

        $this->assertNull($reader->getHeadline());
        $this->assertNull($reader->getCaption());
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Priority can only contain xmp, iptc or exif
     */
    public function testInvalidPriority()
    {
        $reader = new Aggregate;

        $reader->setPriority(['test']);
    }
}
