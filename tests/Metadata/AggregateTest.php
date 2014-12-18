<?php
namespace CSD\Image\Tests\Metadata;

use CSD\Image\JPEG;
use CSD\Image\Metadata\Iptc;
use CSD\Image\Metadata\Aggregate;
use CSD\Image\Metadata\Xmp;

use Mockery as M;

/**
 * Unit tests for {@see \CSD\Image\Metadata\Aggregate}.
 *
 * @coversDefaultClass \CSD\Image\Metadata\Aggregate
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
            ['keywords'],
            ['dateCreated']
        ];
    }

    /**
     * Test the meta fields which only have a value for XMP and IPTC, which is majority.
     *
     * @dataProvider getXmpAndIptcFields
     */
    public function testGetXmpIptcField($field)
    {
        $method = 'get' . ucfirst($field);

        $xmpValue = ($field == 'dateCreated')? new \DateTime: 'XMP value';
        $iptcValue = ($field == 'dateCreated')? new \DateTime: 'IPTC value';

        $xmp = M::mock(Xmp::class);
        $xmp->shouldReceive($method)->once()->andReturn($xmpValue);

        $iptc = M::mock(Iptc::class);
        $iptc->shouldReceive($method)->once()->andReturn($iptcValue);

        $aggregate = new Aggregate($xmp, $iptc);

        $this->assertEquals($xmpValue, $aggregate->$method());

        // change priority so IPTC is first
        $aggregate->setPriority(['iptc', 'xmp']);

        $this->assertEquals($iptcValue, $aggregate->$method());

        // change priority so nothing should be returned
        $aggregate->setPriority([]);

        $this->assertEquals(null, $aggregate->$method());
    }

    /**
     * @dataProvider getXmpAndIptcFields
     */
    public function testXmpIptcFallThrough($field)
    {
        $method = 'get' . ucfirst($field);

        $xmp = M::mock(Xmp::class);
        $xmp->shouldReceive($method)->once()->andReturnNull();

        $iptc = M::mock(Iptc::class);
        $iptc->shouldReceive($method)->once()->andReturn('IPTC value');

        $aggregate = new Aggregate($xmp, $iptc);

        // should always be IPTC as XMP returns null
        $this->assertEquals('IPTC value', $aggregate->$method());
    }

    /**
     * Test that all fields return null if no providers are set.
     *
     * @dataProvider getXmpAndIptcFields
     */
    public function testNullWhenNoProviders($field)
    {
        $reader = new Aggregate;

        $getter = 'get' . ucfirst($field);

        $this->assertNull($reader->$getter());
    }

    /**
     * @dataProvider getXmpAndIptcFields
     */
    public function testSetXmpIptcField($field)
    {
        $method = 'set' . ucfirst($field);
        $value = ($field == 'dateCreated')? new \DateTime: 'value';

        $xmp = M::mock(Xmp::class);
        $xmp->shouldReceive($method)->once()->with($value);

        $iptc = M::mock(Iptc::class);
        $iptc->shouldReceive($method)->once()->with($value);

        $aggregate = new Aggregate($xmp, $iptc);

        $return = $aggregate->$method($value);

        $this->assertSame($aggregate, $return);
    }

    /**
     * @dataProvider getXmpAndIptcFields
     */
    public function testSetXmpIptcFieldWhenNoProviders($field)
    {
        $method = 'set' . ucfirst($field);
        $value = ($field == 'dateCreated')? new \DateTime: 'value';

        $aggregate = new Aggregate;

        $return = $aggregate->$method($value);

        $this->assertSame($aggregate, $return);
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
