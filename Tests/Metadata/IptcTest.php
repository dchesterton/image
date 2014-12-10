<?php
namespace CSD\Photo\Tests\Metadata\Reader;

use CSD\Photo\Metadata\Iptc;

/**
 * @coversDefaultClass \CSD\Photo\Metadata\Reader\IptcReader
 */
class IptcTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Iptc
     */
    private $meta;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->meta = Iptc::fromFile(__DIR__ . '/../Fixtures/metapm.jpg');
    }

    /**
     * @covers ::getHeadline
     */
    public function testHeadline()
    {
        $this->assertEquals('Headline', $this->meta->getHeadline());
    }

    public function tsestCaption()
    {
        $this->assertEquals(
            'José Mourinho',
            $this->meta->getCaption()
        );
    }

    /**
     * @covers ::getKeywords
     */
    public function tesstKeywords()
    {
        $this->assertEquals(
            'Canvey Island, Carshalton Athletic, England, Essex, Football, Ryman Isthmian Premier League, Soccer, ' .
            'Sport, Sports, The Prospects Stadium',
            $this->meta->getKeywords()
        );
    }

    public function testCategory()
    {
        $this->assertEquals('SPO', $this->meta->getCategory());
    }
}
