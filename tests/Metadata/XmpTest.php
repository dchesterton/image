<?php
namespace CSD\Photo\Tests\Metadata;

use CSD\Photo\Metadata\JPEG;
use CSD\Photo\Metadata\JPEGSegment;
use CSD\Photo\Metadata\Xmp;

/**
 * @coversDefaultClass \CSD\Photo\Metadata\Xmp
 */
class XmpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function getDataForAllFile()
    {
        return [
            ['headline', 'Headline'],
            ['caption', 'José Mourinho'],
            ['keywords', ['A keyword', 'Another keyword']],
            ['category', 'SPO'],
            ['contactZip', 'NW1 1AA'],
            ['contactEmail', 'sales@example.com'],
            ['contactCountry', 'England'],
            ['contactAddress', '123 Street Road'],
            ['contactCity', 'London'],
            ['contactUrl', 'http://www.example.com'],
            ['contactPhone', '+44 7901 123456'],
            ['contactState', 'Greater London'],
            ['transmissionReference', 'JOB001'],
            ['objectName', 'OBJECT_NAME'],
            ['instructions', 'All rights reserved.'],
            ['captionWriters', 'Description Writers'],
            ['rightsUsageTerms', 'All rights reserved.'],
            ['event', 'Event Name'],
            ['city', 'London'],
            ['state', 'Greater London'],
            ['location', 'Buckingham Palace'],
            ['country', 'England'],
            ['countryCode', 'GBR'],
            ['IPTCSubjectCodes', ['subj:15054000']],
            ['photographerName', 'Photographer'],
            ['photographerTitle', 'Staff'],
            ['copyrightUrl', 'www.example.com'],
            ['source', 'example.com'],
            ['copyright', 'example.com'],
            ['credit', 'Photographer/Agency'],
            ['urgency', '2'],
            ['rating', '4'],
            ['creatorTool', 'Creator Tool'],
            ['intellectualGenre', 'Intellectual genre'],
            ['supplementalCategories', ['Football', 'Soccer', 'Sport']],
            ['personsShown', ['A person', 'Another person']],
            ['featuredOrganisationName', ['Featured Organisation']],
            ['featuredOrganisationCode', ['Featured Organisation Code']],
            ['IPTCScene', ['IPTC Scene']]
        ];
    }

    /**
     * @return array
     */
    public function getAltFields()
    {
        return [
            ['caption', 'dc:description'],
            ['objectName', 'dc:title'],
            ['copyright', 'dc:rights'],
            ['rightsUsageTerms', 'xmpRights:UsageTerms'],
        ];
    }

    /**
     * @return array
     */
    public function getAttrFields()
    {
        return [
            ['location', 'Iptc4xmpCore:Location'],
            ['contactPhone', 'Iptc4xmpCore:CiTelWork'],
            ['contactAddress', 'Iptc4xmpCore:CiAdrExtadr'],
            ['contactCity', 'Iptc4xmpCore:CiAdrCity'],
            ['contactState', 'Iptc4xmpCore:CiAdrRegion'],
            ['contactZip', 'Iptc4xmpCore:CiAdrPcode'],
            ['contactCountry', 'Iptc4xmpCore:CiAdrCtry'],
            ['contactEmail', 'Iptc4xmpCore:CiEmailWork'],
            ['contactUrl', 'Iptc4xmpCore:CiUrlWork'],
            ['city', 'photoshop:City'],
            ['state', 'photoshop:State'],
            ['country', 'photoshop:Country'],
            ['countryCode', 'Iptc4xmpCore:CountryCode'],
            ['credit', 'photoshop:Credit'],
            ['source', 'photoshop:Source'],
            ['copyrightUrl', 'xmpRights:WebStatement'],
            ['captionWriters', 'photoshop:CaptionWriter'],
            ['instructions', 'photoshop:Instructions'],
            ['category', 'photoshop:Category'],
            ['urgency', 'photoshop:Urgency'],
            ['rating', 'xmp:Rating'],
            ['creatorTool', 'xmp:CreatorTool'],
            ['photographerTitle', 'photoshop:AuthorsPosition'],
            ['transmissionReference', 'photoshop:TransmissionReference'],
            ['headline', 'photoshop:Headline'],
            ['event', 'Iptc4xmpExt:Event'],
            ['intellectualGenre', 'Iptc4xmpCore:IntellectualGenre'],
        ];
    }

    /**
     * @return array
     */
    public function getBagFields()
    {
        return [
            ['keywords', 'dc:subject'],
            ['personsShown', 'Iptc4xmpExt:PersonInImage'],
            ['iptcSubjectCodes', 'Iptc4xmpCore:SubjectCode'],
            ['supplementalCategories', 'photoshop:SupplementalCategories']
        ];
    }

    /**
     * @dataProvider getDataForAllFile
     */
    public function testGetDataFromAllFile($field, $value)
    {
        $getter = 'get' . ucfirst($field);

        $xmp = $this->getXmpFromFile();
        $this->assertEquals($value, $xmp->$getter());

        $xmp = $this->getXmpFromFile2();
        $this->assertEquals($value, $xmp->$getter());
    }

    /**
     * @dataProvider getAltFields
     */
    public function testSetAltFields($field, $xmlField)
    {
        $this->assertValidList('rdf:Alt', $field, $xmlField, $field);
    }

    /**
     * @dataProvider getBagFields
     */
    public function testSetBagFields($field, $xmlField)
    {
        $this->assertValidList('rdf:Bag', $field, $xmlField, $field);
        $this->assertValidList('rdf:Bag', $field, $xmlField, [$field, $field]);
    }

    /**
     * @dataProvider getAttrFields
     */
    public function testSetAttrFields($field, $xmlField)
    {
        $value = 'A test string, with utf €åƒ∂, and some xml chars such as <>"';
        $expectedAttr = $xmlField . '="A test string, with utf €åƒ∂, and some xml chars such as &lt;&gt;&quot;"';
        $expectedElement = '<' . $xmlField . '>A test string, with utf €åƒ∂, and some xml chars such as &lt;&gt;"</' . $xmlField . '>';

        $setter = 'set' . ucfirst($field);

        // test with no meta data
        $xmp = new Xmp;
        $xmp->$setter($value);

        $this->assertContains($expectedAttr, $xmp->getXml());

        // test with empty meta data
        $xmp = new Xmp('<x:xmpmeta xmlns:x="adobe:ns:meta/" />');
        $xmp->$setter($value);

        $this->assertContains($expectedAttr, $xmp->getXml());

        // test with existing meta data
        $xmp = $this->getXmpFromFile();
        $xmp->$setter($value);

        $this->assertContains($expectedAttr, $xmp->getXml());

        // test with existing meta data
        $xmp = $this->getXmpFromFile2();
        $xmp->$setter($value);

        $this->assertContains($expectedElement, $xmp->getXml());
    }

    public function testSetPhotographerName()
    {
        $this->assertValidList('rdf:Seq', 'photographerName', 'dc:creator', 'Photographer Name');
    }

    /**
     * @covers ::getToolkit
     */
    public function testGetToolkit()
    {
        $xmp = $this->getXmpFromFile();

        $this->assertEquals('XMP Core 5.1.2', $xmp->getToolkit());
    }

    /**
     * @covers ::getToolkit
     */
    public function testEmptyToolkit()
    {
        $xmp = new Xmp;
        $this->assertNull($xmp->getToolkit());
    }

    /**
     * @covers ::setToolkit
     */
    public function testSetToolkit()
    {
        $xmp = new Xmp;
        $xmp->setToolkit('Toolkit 1.2.3');

        $this->assertContains('x:xmptk="Toolkit 1.2.3"', $xmp->getXml());
    }

    /**
     * @covers ::getXml
     */
    public function testXmpContainsProcessingInstructions()
    {
        $this->assertXmpContainsProcessingInstructions(new Xmp);
        $this->assertXmpContainsProcessingInstructions(new Xmp('<x:xmpmeta xmlns:x="adobe:ns:meta/" />'));
        $this->assertXmpContainsProcessingInstructions($this->getXmpFromFile());
    }

    /**
     * @covers ::fromArray
     *
     * @dataProvider getDataForAllFile
     */
    public function testFromArray($field, $value)
    {
        $getter = 'get' . ucfirst($field);

        $xmp = Xmp::fromArray([$field => $value]);

        $this->assertEquals($value, $xmp->$getter());
    }

    /**
     * @dataProvider getDataForAllFile
     */
    public function testGetNonExistentValue($field)
    {
        $getter = 'get' . ucfirst($field);

        $xmp = new Xmp;
        $this->assertNull($xmp->$getter());
    }

    /**
     * Test that changing a single piece of metadata changes state of hasChanges.
     *
     * @dataProvider getDataForAllFile
     */
    public function testHasChanges($field, $value)
    {
        $setter = 'set' . ucfirst($field);

        $xmp = new Xmp;

        $this->assertFalse($xmp->hasChanges());

        $xmp->$setter($value);

        $this->assertTrue($xmp->hasChanges());
    }

    /**
     * Test that a rdf:Bag item returns null when the tag is set but there are no items.
     *
     * @covers ::getBag
     */
    public function testGetEmptyBagValue()
    {
        $xmp = new Xmp('<x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="XMP Core 5.1.2">
             <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
              <rdf:Description rdf:about=""
                xmlns:photoshop="http://ns.adobe.com/photoshop/1.0/">
               <photoshop:SupplementalCategories />
              </rdf:Description>
             </rdf:RDF>
            </x:xmpmeta>
        ');

        $this->assertNull($xmp->getSupplementalCategories());
    }

    /**
     * Test that a rdf:Bag item returns null when the tag is set but there are no items.
     *
     * @covers ::getSeq
     */
    public function testGetEmptySeqValue()
    {
        $xmp = new Xmp('<x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="XMP Core 5.1.2">
             <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
              <rdf:Description rdf:about=""
                xmlns:dc="http://purl.org/dc/elements/1.1/">
               <dc:creator />
              </rdf:Description>
             </rdf:RDF>
            </x:xmpmeta>
        ');

        $this->assertNull($xmp->getPhotographerName());
    }

    /**
     * Test that a rdf:Alt item returns null when the tag is set but there are no items.
     *
     * @covers ::getAlt
     */
    public function testGetEmptyAltValue()
    {
        $xmp = new Xmp('<x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="XMP Core 5.1.2">
             <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
              <rdf:Description rdf:about=""
                xmlns:xmpRights="http://ns.adobe.com/xap/1.0/rights/">
               <xmpRights:UsageTerms />
              </rdf:Description>
             </rdf:RDF>
            </x:xmpmeta>
        ');

        $this->assertNull($xmp->getRightsUsageTerms());
    }

    /**
     * @covers ::getContactInfo
     */
    public function testEmptyContactValue()
    {
        $xmp = new Xmp('<x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="XMP Core 5.1.2">
             <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
              <rdf:Description rdf:about=""
                xmlns:Iptc4xmpCore="http://iptc.org/std/Iptc4xmpCore/1.0/xmlns/">
               <Iptc4xmpCore:CreatorContactInfo />
              </rdf:Description>
             </rdf:RDF>
            </x:xmpmeta>
        ');

        $this->assertNull($xmp->getContactCity());
    }

    /**
     * @covers ::getAbout
     * @covers ::setAbout
     */
    public function testAbout()
    {
        $xmp = new Xmp;

        // should be empty string by default
        $this->assertSame('', $xmp->getAbout());

        $xmp->setAbout('about');

        $this->assertSame('about', $xmp->getAbout());
    }

    /**
     * @covers ::getFormatOutput
     * @covers ::setFormatOutput
     */
    public function testFormatOutput()
    {
        $xmp = new Xmp;

        $this->assertFalse($xmp->getFormatOutput());

        $return = $xmp->setFormatOutput(true);

        $this->assertSame($xmp, $return);
        $this->assertTrue($xmp->getFormatOutput());
    }

    public function testDeleteList()
    {
        $xmp = new Xmp;

        $xmp->setSupplementalCategories(['a category', 'another category']);
        $xmp->setSupplementalCategories([]);

        $this->assertNotContains('photoshop:SupplementalCategories', $xmp->getXml());
    }

    /**
     * @dataProvider getAttrFields
     */
    public function testSetNullAttribute($field, $xmlField)
    {
        $setter = 'set' . ucfirst($field);

        $xmp = new Xmp;
        $xmp->$setter($field);
        $xmp->$setter(null);

        $this->assertNotContains($xmlField, $xmp->getXml());

        $xmp = $this->getXmpFromFile();
        $xmp->$setter(null);

        $this->assertNotContains($xmlField, $xmp->getXml());

        $xmp = $this->getXmpFromFile2();
        $xmp->$setter(null);

        $this->assertNotContains($xmlField, $xmp->getXml());
    }

    /**
     * @covers ::getDateCreated
     * @covers ::setDateCreated
     */
    public function testDateCreated()
    {
        $xmp = new Xmp;

        $this->assertNull($xmp->getDateCreated());

        $xmp = new Xmp;
        $xmp->setDateCreated($date = new \DateTime('now'));
        $this->assertEquals($date->format('c'), $xmp->getDateCreated()->format('c'));

        $xmp = new Xmp;
        $xmp->setDateCreated($date = new \DateTime('now'), 'Y');
        $this->assertEquals($date->format('Y'), $xmp->getDateCreated()->format('Y'));

        $xmp = new Xmp;
        $xmp->setDateCreated($date = new \DateTime('now'), 'Y-m');
        $this->assertEquals($date->format('Y-m'), $xmp->getDateCreated()->format('Y-m'));

        $xmp = new Xmp;
        $xmp->setDateCreated($date = new \DateTime('now'), 'Y-m-d');
        $this->assertEquals($date->format('Y-m-d'), $xmp->getDateCreated()->format('Y-m-d'));

        // test with invalid date
        $xmp = new Xmp('
            <x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="XMP Core 5.1.2">
              <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
                <rdf:Description rdf:about=""
                  xmlns:photoshop="http://ns.adobe.com/photoshop/1.0/"
                  photoshop:DateCreated="DATE" />
              </rdf:RDF>
            </x:xmpmeta>
        ');

        $this->assertFalse($xmp->getDateCreated());

    }

    /**
     * Test that the reader only accepts valid XMP root tag.
     *
     * @expectedException \RuntimeException
     */
    public function testInvalidXmlException()
    {
        new Xmp('<myelement />');
    }

    /**
     * @covers ::fromFile
     */
    public function testFromFile()
    {
        $this->assertInstanceOf(Xmp::class, Xmp::fromFile(__DIR__ . '/../Fixtures/all.XMP'));
    }

    /**
     * @param Xmp $xmp
     */
    private function assertXmpContainsProcessingInstructions(Xmp $xmp)
    {
        $this->assertContains("<?xpacket begin=\"\xef\xbb\xbf\" id=\"W5M0MpCehiHzreSzNTczkc9d\"?>", $xmp->getXml());
        $this->assertContains('<?xpacket end="w"?>', $xmp->getXml());
    }

    /**
     * @param $type
     * @param $field
     * @param $xmlField
     * @param $value
     */
    private function assertValidList($type, $field, $xmlField, $value)
    {
        $attributes = ($type == 'rdf:Alt')? ' xml:lang="x-default"': '';

        $expected  = '<' . $xmlField . '><' . $type . '>';

        foreach ((array) $value as $li) {
            $expected .= '<rdf:li' . $attributes . '>' . $li . '</rdf:li>';
        }

        $expected .= '</' . $type . '></' . $xmlField . '>';

        $setter = 'set' . ucfirst($field);

        $xmp = new Xmp;
        $xmp->$setter($value);

        $this->assertContains($expected, $xmp->getXml());

        // test setting value on existing meta data
        $xmp = $this->getXmpFromFile();
        $xmp->$setter($value);

        $this->assertContains($expected, $xmp->getXml());

        // test setting value on existing meta data
        $xmp = $this->getXmpFromFile2();
        $xmp->$setter($value);

        $this->assertContains($expected, $xmp->getXml());
    }

    /**
     * Gets XMP file where the data is written as attributes.
     *
     * @return Xmp
     */
    private function getXmpFromFile()
    {
        return new Xmp(file_get_contents(__DIR__ . '/../Fixtures/all.XMP'));
    }

    /**
     * Gets XMP file where the data is written as elements.
     *
     * @return Xmp
     */
    private function getXmpFromFile2()
    {
        return new Xmp(file_get_contents(__DIR__ . '/../Fixtures/all2.XMP'));
    }
}
