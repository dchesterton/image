<?php
namespace CSD\Photo\Tests\Metadata\Reader;

use CSD\Photo\Image\JPEG;
use CSD\Photo\Metadata\Iptc;
use CSD\Photo\Metadata\Reader\AggregateReader;
use CSD\Photo\Metadata\Xmp;

/**
 * Unit tests for {@see \CSD\Photo\Metadata\Reader\AggregateReader}.
 *
 * @coversDefaultClass \CSD\Photo\Metadata\Reader\AggregateReader
 */
class AggregateReaderTest extends \PHPUnit_Framework_TestCase
{
    public function getXmpAndIptcFields()
    {
        return [
            ['headline'],
            ['caption']
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

        $aggregateReader = new AggregateReader($xmp, $iptc);

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

        $aggregateReader = new AggregateReader($xmp, $iptc);

        // should always be IPTC as XMP returns null
        $this->assertEquals('IPTC value', $aggregateReader->$method());
    }

    /**
     * Test that all fields return null if no providers are set.
     */
    public function testNullWhenNoProviders()
    {
        $reader = new AggregateReader;

        $this->assertNull($reader->getHeadline());
        $this->assertNull($reader->getCaption());
    }
}
