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
     * @var Xmp
     */
    private $xmp;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->xmp = new Xmp(file_get_contents(__DIR__ . '/../Fixtures/all.XMP'));
        $this->xmp->setFormatOutput(false);
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
     * @covers ::getHeadline
     */
    public function testHeadline()
    {
        $this->assertEquals(
            'Dereham Town v Harlow Town. Ryman League Division One North',
            $this->xmp->getHeadline()
        );
    }

    /**
     * @covers ::getCaption
     */
    public function testCaption()
    {
        $this->assertEquals(
            'MARCH 01: José Mourinho during the Ryman League Division One North match between Dereham Town and ' .
            'Harlow Town at Aldiss Park in Dereham, England.',
            $this->xmp->getCaption()
        );
    }

    /**
     * @covers ::getKeywords
     */
    public function testKeywords()
    {
        $this->assertEquals(
            ['Ryman League Division One North', 'Football', 'Soccer', 'Sport', 'Sports', 'Dereham Town',
                'Harlow Town', 'Aldiss Park', 'Dereham', 'Norfolk', 'England'],
            $this->xmp->getKeywords()
        );
    }

    /**
     * @covers ::getCategory
     */
    public function testCategory()
    {
        $this->assertEquals(
            'SPO',
            $this->xmp->getCategory()
        );
    }

    /**
     * @covers ::getContactZip
     */
    public function testContactZip()
    {
        $this->assertEquals(
            'NW1 1AA',
            $this->xmp->getContactZip()
        );
    }

    /**
     * @covers ::getContactEmail
     */
    public function testContactEmail()
    {
        $this->assertEquals(
            'sales@example.com',
            $this->xmp->getContactEmail()
        );
    }

    /**
     * @covers ::getContactCountry
     */
    public function testContactCountry()
    {
        $this->assertEquals(
            'England',
            $this->xmp->getContactCountry()
        );
    }

    /**
     * @covers ::getContactAddress
     */
    public function testContactAddress()
    {
        $this->assertEquals(
            '123 Street Road',
            $this->xmp->getContactAddress()
        );
    }

    /**
     * @covers ::getContactCity
     */
    public function testContactCity()
    {
        $this->assertEquals(
            'London',
            $this->xmp->getContactCity()
        );
    }

    /**
     * @covers ::getContactUrl
     */
    public function testContactUrl()
    {
        $this->assertEquals(
            'http://www.example.com',
            $this->xmp->getContactUrl()
        );
    }

    /**
     * @covers ::getContactPhone
     */
    public function testContactPhone()
    {
        $this->assertEquals('+44 7901 123456', $this->xmp->getContactPhone());
    }

    /**
     * @covers ::getContactState
     */
    public function testContactState()
    {
        $this->assertEquals('Greater London', $this->xmp->getContactState());
    }

    /**
     * @covers ::getTransmissionReference
     */
    public function testTransmissionReference()
    {
        $this->assertEquals('XFD-1291', $this->xmp->getTransmissionReference());
    }

    /**
     * @covers ::getObjectName
     */
    public function testObjectName()
    {
        $this->assertEquals('DEREHAM_HARLOW_20140301', $this->xmp->getObjectName());
    }

    /**
     * @covers ::getInstructions
     */
    public function testInstructions()
    {
        $this->assertEquals(
            'No unpaid use. All rights reserved.',
            $this->xmp->getInstructions()
        );
    }

    /**
     * @covers ::getCaptionWriters
     */
    public function testCaptionWriters()
    {
        $this->assertEquals(
            'PHOTOG',
            $this->xmp->getCaptionWriters()
        );
    }

    /**
     * @covers ::getRightsUsageTerms
     */
    public function testRightsUsageTerms()
    {
        $this->assertEquals(
            'No unpaid use. All rights reserved.',
            $this->xmp->getRightsUsageTerms()
        );
    }

    /**
     * @covers ::getEvent
     */
    public function testEvent()
    {
        $this->assertEquals(
            'Dereham Town v Harlow Town. Ryman League Division One North',
            $this->xmp->getEvent()
        );
    }

    /**
     * @covers ::getCity
     */
    public function testCity()
    {
        $this->assertEquals('Dereham', $this->xmp->getCity());
    }

    /**
     * @covers ::getState
     */
    public function testState()
    {
        $this->assertEquals(
            'Norfolk',
            $this->xmp->getState()
        );
    }

    /**
     * @covers ::getLocation
     */
    public function testLocation()
    {
        $this->assertEquals('Aldiss Park', $this->xmp->getLocation());
    }

    /**
     * @covers ::getCountry
     */
    public function testCountry()
    {
        $this->assertEquals('England', $this->xmp->getCountry());
    }

    /**
     * @covers ::getCountryCode
     */
    public function testCountryCode()
    {
        $this->assertEquals('GBR', $this->xmp->getCountryCode());
    }

    /**
     * @covers ::getIptcSubjectCodes
     */
    public function testSubjectCodes()
    {
        $this->assertEquals(
            array('subj:15054000'),
            $this->xmp->getIptcSubjectCodes()
        );
    }

    /**
     * @covers ::getPhotographerName
     */
    public function testPhotographerName()
    {
        $this->assertEquals('Photographer', $this->xmp->getPhotographerName());
    }

    /**
     * @covers ::getPhotographerTitle
     */
    public function testPhotographerTitle()
    {
        $this->assertEquals('Staff', $this->xmp->getPhotographerTitle());
    }

    /**
     * @covers ::getCopyrightUrl
     */
    public function testCopyrightUrl()
    {
        $this->assertEquals('www.example.com', $this->xmp->getCopyrightUrl());
    }

    /**
     * @covers ::getSource
     */
    public function testSource()
    {
        $this->assertEquals('example.com', $this->xmp->getSource());
    }

    /**
     * @covers ::getCopyright
     */
    public function testCopyright()
    {
        $this->assertEquals(
            'example.com',
            $this->xmp->getCopyright()
        );
    }

    /**
     * @covers ::getCredit
     */
    public function testCredit()
    {
        $this->assertEquals(
            'Photographer/Agency',
            $this->xmp->getCredit()
        );
    }

    /**
     * @covers ::getUrgency
     */
    public function testUrgency()
    {
        $this->assertEquals(
            '2',
            $this->xmp->getUrgency()
        );
    }

    /**
     * @covers ::getRating
     */
    public function testRating()
    {
        $this->assertEquals(
            '4',
            $this->xmp->getRating()
        );
    }

    /**
     * @covers ::getIntellectualGenre
     */
    public function testIntellectualGenre()
    {
        $this->assertEquals(
            'Intellectual genre',
            $this->xmp->getIntellectualGenre()
        );
    }

    /**
     * @covers ::getSupplementalCategories
     */
    public function testSupplementalCategories()
    {
        $this->assertEquals(
            ['Football', 'Soccer', 'Ryman League Division One North'],
            $this->xmp->getSupplementalCategories()
        );
    }

    /**
     * @covers ::getPersonsShown
     */
    public function testPersonsShown()
    {
        $this->assertEquals(
            array('José Mourinho', 'Somebody Else'),
            $this->xmp->getPersonsShown()
        );
    }

    /**
     * SETTERS
     */
    /**
     * @covers ::setCaption
     */
    public function testSetCaption()
    {
        $this->assertValidAlt('caption', 'dc:description', 'Caption here');
    }

    /**
     * @covers ::setObjectName
     */
    public function testSetObjectName()
    {
        $this->assertValidAlt('objectName', 'dc:title', 'Object name');
    }

    /**
     * @covers ::setCopyright
     */
    public function testSetCopyright()
    {
        $this->assertValidAlt('copyright', 'dc:rights', 'Copyright');
    }

    /**
     * @covers ::setRightsUsageTerms
     */
    public function testSetRightsUsageTerms()
    {
        $this->assertValidAlt('rightsUsageTerms', 'xmpRights:UsageTerms', 'All rights reserved');
    }

    /**
     * @covers ::setLocation
     */
    public function testSetLocation()
    {
        $this->assertValidAttr('location', 'Iptc4xmpCore:Location');
    }

    /**
     * @covers ::setContactPhone
     */
    public function testSetContactPhone()
    {
        $this->assertValidAttr('contactPhone', 'Iptc4xmpCore:CiTelWork');
    }

    public function testSetContactAddress()
    {
        $this->assertValidAttr('contactAddress', 'Iptc4xmpCore:CiAdrExtadr');
    }

    public function testSetContactCity()
    {
        $this->assertValidAttr('contactCity', 'Iptc4xmpCore:CiAdrCity');
    }

    public function testSetContactState()
    {
        $this->assertValidAttr('contactState', 'Iptc4xmpCore:CiAdrRegion');
    }

    public function testSetContactZip()
    {
        $this->assertValidAttr('contactZip', 'Iptc4xmpCore:CiAdrPcode');
    }

    public function testSetContactCountry()
    {
        $this->assertValidAttr('contactCountry', 'Iptc4xmpCore:CiAdrCtry');
    }

    public function testSetContactEmail()
    {
        $this->assertValidAttr('contactEmail', 'Iptc4xmpCore:CiEmailWork');
    }

    public function testSetContactUrl()
    {
        $this->assertValidAttr('contactUrl', 'Iptc4xmpCore:CiUrlWork');
    }

    public function testSetCity()
    {
        $this->assertValidAttr('city', 'photoshop:City');
    }

    public function testSetState()
    {
        $this->assertValidAttr('state', 'photoshop:State');
    }

    public function testSetCountry()
    {
        $this->assertValidAttr('country', 'photoshop:Country');
    }

    public function testSetCountryCode()
    {
        $this->assertValidAttr('countryCode', 'Iptc4xmpCore:CountryCode');
    }

    public function testSetCredit()
    {
        $this->assertValidAttr('credit', 'photoshop:Credit');
    }

    public function testSetSource()
    {
        $this->assertValidAttr('source', 'photoshop:Source');
    }

    public function testSetCopyrightUrl()
    {
        $this->assertValidAttr('copyrightUrl', 'xmpRights:WebStatement');
    }

    public function testSetCaptionWriters()
    {
        $this->assertValidAttr('captionWriters', 'photoshop:CaptionWriter');
    }

    public function testSetInstructions()
    {
        $this->assertValidAttr('instructions', 'photoshop:Instructions');
    }

    public function testSetCategory()
    {
        $this->assertValidAttr('category', 'photoshop:Category');
    }

    public function testSetUrgency()
    {
        $this->assertValidAttr('urgency', 'photoshop:Urgency');
    }

    public function testSetRating()
    {
        $this->assertValidAttr('rating', 'xmp:Rating');
    }

    public function testSetPhotographerTitle()
    {
        $this->assertValidAttr('photographerTitle', 'photoshop:AuthorsPosition');
    }

    public function testSetTransmissionReference()
    {
        $this->assertValidAttr('transmissionReference', 'photoshop:TransmissionReference');
    }

    public function testSetHeadline()
    {
        $this->assertValidAttr('headline', 'photoshop:Headline');
    }

    public function testSetEvent()
    {
        $this->assertValidAttr('event', 'Iptc4xmpExt:Event');
    }

    public function testSetIntellectualGenre()
    {
        $this->assertValidAttr('intellectualGenre', 'Iptc4xmpCore:IntellectualGenre');
    }

    public function testSetKeywords()
    {
        $this->assertValidBag('keywords', 'dc:subject', 'Keyword');
        $this->assertValidBag('keywords', 'dc:subject', ['Keyword 1', 'Keyword 2']);
    }

    public function testSetPersonsShown()
    {
        $this->assertValidBag('personsShown', 'Iptc4xmpExt:PersonInImage', 'Bob');
        $this->assertValidBag('personsShown', 'Iptc4xmpExt:PersonInImage', ['Bob', 'Jeff', 'Jill']);
    }

    public function testSetSubjectCodes()
    {
        $this->assertValidBag('iptcSubjectCodes', 'Iptc4xmpCore:SubjectCode', 'Subject');
        $this->assertValidBag('iptcSubjectCodes', 'Iptc4xmpCore:SubjectCode', ['Subject 1', 'Subject 2']);
    }

    public function testSetSupplementalCategories()
    {
        $this->assertValidBag('supplementalCategories', 'photoshop:SupplementalCategories', 'Cat');
        $this->assertValidBag('supplementalCategories', 'photoshop:SupplementalCategories', ['Cat 1', 'Cat 2']);
    }

    public function testSetPhotographerName()
    {
        $this->assertValidSeq('photographerName', 'dc:creator', 'Photographer Name');
    }

    private function assertValidAlt($field, $xmlField, $value)
    {
        $this->assertValidList('rdf:Alt', $field, $xmlField, $value);
    }

    private function assertValidSeq($field, $xmlField, $value)
    {
        $this->assertValidList('rdf:Seq', $field, $xmlField, $value);
    }

    private function assertValidBag($field, $xmlField, $value)
    {
        $this->assertValidList('rdf:Bag', $field, $xmlField, $value);
    }

    private function assertValidList($type, $field, $xmlField, $value)
    {
        $attributes = ($type == 'rdf:Alt')? ' xml:lang="x-default"': '';

        $expected  = '<' . $xmlField . '><' . $type . '>';

        foreach ((array) $value as $li) {
            $expected .= '<rdf:li' . $attributes . '>' . $li . '</rdf:li>';
        }

        $expected .= '</' . $type . '></' . $xmlField . '>';

        $setter = 'set' . ucfirst($field);

        $xmp = new Xmp(null, false);
        $xmp->$setter($value);

        $this->assertContains($expected, $xmp->getXml());

        // test setting caption on existing meta data
        $this->xmp->$setter($value);
        $this->assertContains($expected, $this->xmp->getXml());
    }

    private function assertValidAttr($field, $xmlField)
    {
        $value = 'A test string, with utf €åƒ∂, and some xml chars such as <>"';
        $expected = $xmlField . '="A test string, with utf €åƒ∂, and some xml chars such as &lt;&gt;&quot;"';

        $setter = 'set' . ucfirst($field);

        // test setting field on new meta data
        $xmp = new Xmp(null, false);
        $xmp->$setter($value);

        $this->assertContains($expected, $xmp->getXml());

        // test setting field on empty meta data
        $xmp = new Xmp('<x:xmpmeta xmlns:x="adobe:ns:meta/" />', false);
        $xmp->$setter($value);

        $this->assertContains($expected, $xmp->getXml());

        // test setting caption on existing meta data
        $this->xmp->$setter($value);
        $this->assertContains($expected, $this->xmp->getXml());
    }

    /**
     * @covers ::getToolkit
     */
    public function testToolkit()
    {
        $this->assertEquals(
            'XMP Core 5.1.2',
            $this->xmp->getToolkit()
        );
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
        $this->xmp->setToolkit('Toolkit 1.2.3');
        $this->assertContains('x:xmptk="Toolkit 1.2.3"', $this->xmp->getXml());
    }

    public function testContainsProcessingInstructions()
    {
        $this->assertContains(
            "<?xpacket begin=\"\xef\xbb\xbf\" id=\"W5M0MpCehiHzreSzNTczkc9d\"?>",
            $this->xmp->getXml()
        );

        $this->assertContains(
            '<?xpacket end="w"?>',
            $this->xmp->getXml()
        );

    }

    /**
     * @covers ::getXml
     */
    public function testNewObjectContainsProcessingInstructions()
    {
        $xmp = new Xmp;

        $this->assertContains(
            "<?xpacket begin=\"\xef\xbb\xbf\" id=\"W5M0MpCehiHzreSzNTczkc9d\"?>",
            $xmp->getXml()
        );

        $this->assertContains(
            '<?xpacket end="w"?>',
            $xmp->getXml()
        );
    }

    /**
     * @covers ::getXml
     */
    public function testEmptyElementContainsProcessingInstructions()
    {
        // test with empty document, should still add required processing instructions
        $xmp = new Xmp('<x:xmpmeta xmlns:x="adobe:ns:meta/" />');

        $this->assertContains(
            "<?xpacket begin=\"\xef\xbb\xbf\" id=\"W5M0MpCehiHzreSzNTczkc9d\"?>",
            $xmp->getXml()
        );

        $this->assertContains(
            '<?xpacket end="w"?>',
            $xmp->getXml()
        );
    }

    /**
     * @covers ::fromArray
     */
    public function testFromArray()
    {
        // test with a couple of properties. Todo: test with all properties
        $arr = [
            'contactPhone' => '01234 567890',
            'headline' => 'A headline',
        ];

        $xmp = Xmp::fromArray($arr);

        $this->assertEquals($arr['contactPhone'], $xmp->getContactPhone());
        $this->assertEquals($arr['headline'], $xmp->getHeadline());
    }

    /**
     * @covers ::getHeadline
     * @covers ::getCaption
     * @covers ::getCaptionWriters
     * @covers ::getSource
     * @covers ::getPhotographerName
     * @covers ::getCopyright
     */
    public function testNonExistentValue()
    {
        // todo: add a check for every getter

        $xmp = new Xmp;
        $this->assertEquals(null, $xmp->getHeadline());
        $this->assertEquals(null, $xmp->getCaption());
        $this->assertEquals(null, $xmp->getCaptionWriters());
        $this->assertEquals(null, $xmp->getSource());
        $this->assertEquals(null, $xmp->getPhotographerName());
        $this->assertEquals(null, $xmp->getCopyright());
    }
}
