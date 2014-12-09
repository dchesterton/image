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

    public function setUp()
    {
        //$this->meta = IptcReader::fromFile(__DIR__ . '/PHC_CANVEY_CARSHALTON_010314_047.JPG');
        $this->meta = Iptc::fromFile(__DIR__ . '/DJC36439.jpg');
    }

    /**
     * @covers ::getHeadline
     */
    public function testHeadline()
    {
        $this->assertEquals(
            'Canvey Island v Carshalton Athletic. Ryman Isthmian Premier League',
            $this->meta->getHeadline()
        );
    }

    /**
     * @covers ::getCaption
     */
    public function testCaption()
    {
        $this->assertEquals(
            'MARCH 01: å during the Ryman Isthmian Premier League match between Canvey Island and Carshalton Athletic' .
            ' at The Prospects Stadium in Canvey Island, England. (Photo by Daniel Chesterton/phcimages.com)',
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

    /**
     * @covers ::getCategory
     */
    public function testCategory()
    {
        $this->assertEquals(
            'SPO',
            $this->meta->getCategory()
        );
    }
}
