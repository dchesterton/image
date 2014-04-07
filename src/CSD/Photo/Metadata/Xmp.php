<?php
/**
 * This file is part of the Photo Store package.
 *
 * (c) Daniel Chesterton <daniel@chestertondevelopment.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CSD\Photo\Metadata;

use CSD\Photo\Metadata\Reader\MetadataReaderInterface;
use DomDocument;
use DOMXPath;

/**
 * Class to read XMP metadata from an image.
 *
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class Xmp implements MetadataReaderInterface
{
    /**
     *
     */
    const IPTC4_XMP_CORE_NS = 'http://iptc.org/std/Iptc4xmpCore/1.0/xmlns/';
    /**
     *
     */
    const IPTC4_XMP_EXT_NS = 'http://iptc.org/std/Iptc4xmpExt/2008-02-29/';
    /**
     *
     */
    const PHOTOSHOP_NS = 'http://ns.adobe.com/photoshop/1.0/';
    /**
     *
     */
    const DC_NS = 'http://purl.org/dc/elements/1.1/';
    /**
     *
     */
    const XMP_RIGHTS_NS = 'http://ns.adobe.com/xap/1.0/rights/';
    /**
     *
     */
    const RDF_NS = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';
    /**
     *
     */
    const XMP_NS = "http://ns.adobe.com/xap/1.0/";

    /**
     * @var DomDocument
     */
    private $dom;

    /**
     * @var DOMXPath
     */
    private $xpath;

    /**
     * @var string
     */
    private $about = '';

    /**
     * The XMP namespaces used by this class.
     *
     * @var array
     */
    private $namespaces = array(
        'rdf' => self::RDF_NS,
        'dc' => self::DC_NS,
        'photoshop' => self::PHOTOSHOP_NS,
        'xmp' => self::XMP_NS,
        'xmpRights' => self::XMP_RIGHTS_NS,
        'Iptc4xmpCore' => self::IPTC4_XMP_CORE_NS,
        'Iptc4xmpExt' => self::IPTC4_XMP_EXT_NS
    );

    /**
     * @param string|null $data
     * @param bool        $formatOutput
     *
     * @throws \Exception
     */
    public function __construct($data = null, $formatOutput = true)
    {
        $this->dom = new DomDocument('1.0', 'UTF-8');
        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = $formatOutput;
        $this->dom->substituteEntities = false;

        if (!$data) {
            $data = '<x:xmpmeta xmlns:x="adobe:ns:meta/" />';
        }

        // load xml
        $this->dom->loadXML($data);
        $this->dom->encoding = 'UTF-8';

        if ($this->dom->documentElement->nodeName != 'x:xmpmeta') {
            throw new \RuntimeException('Root node must be of type x:xmpmeta.');
        }

        // set up xpath
        $this->xpath = new DOMXPath($this->dom);

        foreach ($this->namespaces as $prefix => $url) {
            $this->xpath->registerNamespace($prefix, $url);
        }

        // try and find an rdf:about attribute, and set it as the default if found
        $about = $this->xpath->query('//rdf:Description/@rdf:about')->item(0);

        if ($about) {
            $this->about = $about->nodeValue;
        }
    }

    /**
     * @param boolean $formatOutput
     */
    public function setFormatOutput($formatOutput)
    {
        $this->dom->formatOutput = $formatOutput;
    }

    /**
     * Load XMP data from an image.
     *
     * @param JPEG $jpeg.
     *
     * @return Xmp
     */
    public static function fromJPEG(JPEG $jpeg)
    {
        $possible = $jpeg->getSegmentsByName('APP1');
        $xmpData = null;

        foreach ($possible as $segment) {
            $data = $segment->getData();

            if (strncmp($data, "http://ns.adobe.com/xap/1.0/\x00", 29) == 0) {
                $xmpData = substr($data, 29);
                break;
            }
        }

        return new self($xmpData);
    }

    /**
     * @param string $fileName XMP file to load
     *
     * @return Xmp
     */
    public static function fromFile($fileName)
    {
        return new self(file_get_contents($fileName));
    }

    /**
     * @param $array
     *
     * @return Xmp
     */
    public static function fromArray($array)
    {
        $xmp = new self();

        foreach ($array as $field => $value) {
            $setter = 'set' . ucfirst($field);

            if (method_exists($xmp, $setter) && $value) {
                $xmp->$setter($value);
            }
        }

        return $xmp;
    }

    /**
     * @param      $field
     * @param      $ns
     * @param bool $checkAttributes
     *
     * @return \DOMNode|null
     */
    private function getNode($field, $ns, $checkAttributes = true)
    {
        $rdfDesc = $this->getRDFDescription($ns);

        // check for field as an element or an attribute
        $query = ($checkAttributes)? $field . '|@' . $field: $field;
        $result = $this->xpath->query($query, $rdfDesc);

        if ($result->length) {
            return $result->item(0);
        }

        return null;
    }

    /**
     * Returns data for the given XMP field. Returns null if the field does not exist.
     *
     * @param string $field The field to return.
     * @param string $namespace
     *
     * @return string|null
     */
    private function get($field, $namespace)
    {
        $node = $this->getNode($field, $namespace);

        if ($node) {
            return $node->nodeValue;
        }
        return '';
    }

    /**
     * @param $field
     * @param $namespace
     *
     * @return array|null
     */
    private function getBag($field, $namespace)
    {
        $node = $this->getNode($field, $namespace, false);

        if ($node) {
            $bag = $this->xpath->query('rdf:Bag', $node)->item(0);

            if ($bag) {
                for ($items = [], $i = 0; $i < $bag->childNodes->length; $i++) {
                    $items[] = $bag->childNodes->item($i)->nodeValue;
                }

                return $items;
            }
        }

        return '';
    }

    /**
     * @param $field
     * @param $namespace
     *
     * @return null|string
     */
    private function getAlt($field, $namespace)
    {
        $node = $this->getNode($field, $namespace, false);

        if ($node) {
            $bag = $this->xpath->query('rdf:Alt', $node)->item(0);

            if ($bag) {
                return $bag->childNodes->item(0)->nodeValue;
            }
        }

        return '';
    }

    /**
     * @param $field
     * @param $namespace
     *
     * @return array|null
     */
    private function getSeq($field, $namespace)
    {
        $node = $this->getNode($field, $namespace, false);

        if ($node) {
            $bag = $this->xpath->query('rdf:Seq', $node)->item(0);

            if ($bag) {
                for ($items = [], $i = 0; $i < $bag->childNodes->length; $i++) {
                    $items[] = $bag->childNodes->item($i)->nodeValue;
                }

                return $items;
            }
        }

        return '';
    }

    /**
     * @param $field
     * @param $value
     * @param $ns
     */
    private function setAttr($field, $value, $ns)
    {
        // check if this already exists first
        $existingNode = $this->getNode($field, $ns);

        if ($existingNode) {
            $existingNode->nodeValue = $value;
        } else {
            // create new attribute
            $this->getOrCreateRDFDescription($ns)->setAttributeNS($ns, $field, $value);
        }
    }

    /**
     * @param $field
     * @param $value
     */
    private function setContactAttr($field, $value)
    {
        $contactNode = $this->xpath->query('//Iptc4xmpCore:CreatorContactInfo');

        if ($contactNode->length) {
            $parent = $contactNode->item(0);
        } else {
            $parent = $this->dom->createElementNS(self::IPTC4_XMP_CORE_NS, 'Iptc4xmpCore:CreatorContactInfo');
            $this->getOrCreateRDFDescription(self::IPTC4_XMP_CORE_NS)->appendChild($parent);
        }

        $parent->setAttribute($field, $value);
    }

    /**
     * @param $namespace
     *
     * @return \DOMNode|null
     */
    private function getRDFDescription($namespace)
    {
        // element
        $description = $this->xpath->query("//rdf:Description[*[namespace-uri()='$namespace']]");

        if ($description->length > 0) {
            return $description->item(0);
        }

        // attribute
        $description = $this->xpath->query("//rdf:Description[@*[namespace-uri()='$namespace']]");

        if ($description->length > 0) {
            return $description->item(0);
        }

        return null;
    }

    /**
     * @param $namespace
     *
     * @return \DOMElement|\DOMNode|null
     */
    private function getOrCreateRDFDescription($namespace)
    {
        $desc = $this->getRDFDescription($namespace);

        if ($desc) {
            return $desc;
        }

        // try and find any rdf:Description, and add to that
        $desc = $this->xpath->query('//rdf:Description')->item(0);

        if ($desc) {
            return $desc;
        }

        // no rdf:Description's, create new
        $prefix = array_search($namespace, $this->namespaces);

        $desc = $this->dom->createElementNS(self::RDF_NS, 'rdf:Description');
        $desc->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:' . $prefix, $namespace);

        $rdf = $this->xpath->query('rdf:RDF', $this->dom->documentElement)->item(0);

        // check if rdf:RDF element exists, and create it if not
        if (!$rdf) {
            $rdf = $this->dom->createElementNS(self::RDF_NS, 'rdf:RDF');
            $this->dom->documentElement->appendChild($rdf);
        }

        $rdf->appendChild($desc);

        return $desc;
    }

    /**
     * @param $field
     * @param $value
     * @param $ns
     */
    private function setBag($field, $value, $ns)
    {
        $this->setList($field, $value, 'rdf:Bag', $ns);
    }

    /**
     * @param $field
     * @param $value
     * @param $ns
     */
    private function setAlt($field, $value, $ns)
    {
        $this->setList($field, $value, 'rdf:Alt', $ns);
    }

    /**
     * @param $field
     * @param $value
     * @param $ns
     */
    private function setSeq($field, $value, $ns)
    {
        $this->setList($field, $value, 'rdf:Seq', $ns);
    }

    /**
     * @param $field
     * @param $value
     * @param $type
     * @param $ns
     */
    private function setList($field, $value, $type, $ns)
    {
        $result = $this->xpath->query('/rdf:Description/' . $field . '/' . $type . '/rdf:li');
        $parent = null;

        if (!$value || (!is_array($value) && count($value) == 0)) {
            // todo: remove node
        }

        if ($result->length) {
            $parent = $result->item(0)->parentNode;

            // remove child nodes
            for ($i = 0; $i < $result->length; $i++) {
                $parent->removeChild($result->item($i));
            }
        } else {
            // find the RDF description root
            $description = $this->getOrCreateRDFDescription($ns);

            // create the element and the rdf:Alt child
            $node = $this->dom->createElementNS($ns, $field);
            $parent = $this->dom->createElementNS(self::RDF_NS, $type);

            $description->appendChild($node);
            $node->appendChild($parent);
        }

        if ($parent) {
            foreach ((array) $value as $item) {
                $node = $this->dom->createElementNS(self::RDF_NS, 'rdf:li');
                $node->appendChild($this->dom->createTextNode($item));

                if ($type == 'rdf:Alt') {
                    $node->setAttribute('xml:lang', 'x-default');
                }

                $parent->appendChild($node);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getHeadline()
    {
        return $this->get('photoshop:Headline', self::PHOTOSHOP_NS);
    }

    /**
     * Set headline.
     *
     * @param $headline string
     *
     * @return Xmp
     */
    public function setHeadline($headline)
    {
        $this->setAttr('photoshop:Headline', $headline, self::PHOTOSHOP_NS);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCaption()
    {
        return $this->getAlt('dc:description', self::DC_NS);
    }

    /**
     * @param $caption string
     *
     * @return Xmp
     */
    public function setCaption($caption)
    {
        $this->setAlt('dc:description', $caption, self::DC_NS);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEvent()
    {
        return $this->get('Iptc4xmpExt:Event', self::IPTC4_XMP_EXT_NS);
    }

    /**
     * @param $event string
     *
     * @return Xmp
     */
    public function setEvent($event)
    {
        $this->setAttr('Iptc4xmpExt:Event', $event, self::IPTC4_XMP_EXT_NS);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocation()
    {
        return $this->get('Iptc4xmpCore:Location', self::IPTC4_XMP_CORE_NS);
    }

    /**
     * @param $location string
     *
     * @return Xmp
     */
    public function setLocation($location)
    {
        $this->setAttr('Iptc4xmpCore:Location', $location, self::IPTC4_XMP_CORE_NS);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCity()
    {
        return $this->get('photoshop:City', self::PHOTOSHOP_NS);
    }

    /**
     * @param $city string
     *
     * @return Xmp
     */
    public function setCity($city)
    {
        $this->setAttr('photoshop:City', $city, self::PHOTOSHOP_NS);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return $this->get('photoshop:State', self::PHOTOSHOP_NS);
    }

    /**
     * @param $state string
     *
     * @return Xmp
     */
    public function setState($state)
    {
        $this->setAttr('photoshop:State', $state, self::PHOTOSHOP_NS);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountry()
    {
        return $this->get('photoshop:Country', self::PHOTOSHOP_NS);
    }

    /**
     * @param $country string
     *
     * @return Xmp
     */
    public function setCountry($country)
    {
        $this->setAttr('photoshop:Country', $country, self::PHOTOSHOP_NS);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountryCode()
    {
        return $this->get('Iptc4xmpCore:CountryCode', self::IPTC4_XMP_CORE_NS);
    }

    /**
     * @param $countryCode string
     *
     * @return Xmp
     */
    public function setCountryCode($countryCode)
    {
        $this->setAttr('Iptc4xmpCore:CountryCode', $countryCode, self::IPTC4_XMP_CORE_NS);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIPTCSubjectCodes()
    {
        return $this->getBag('Iptc4xmpCore:SubjectCode', self::IPTC4_XMP_CORE_NS);
    }

    /**
     * @param $subjectCodes array
     *
     * @return Xmp
     */
    public function setIPTCSubjectCodes($subjectCodes)
    {
        $this->setBag('Iptc4xmpCore:SubjectCode', $subjectCodes, self::IPTC4_XMP_CORE_NS);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPhotographerName()
    {
        $seq = $this->getSeq('dc:creator', self::DC_NS);

        if (is_array($seq)) {
            return $seq[0];
        }
        return $seq;
    }

    /**
     * @param $photographerName string
     *
     * @return Xmp
     */
    public function setPhotographerName($photographerName)
    {
        $this->setSeq('dc:creator', $photographerName, self::DC_NS);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredit()
    {
        return $this->get('photoshop:Credit', self::PHOTOSHOP_NS);
    }

    /**
     * @param $credit string
     *
     * @return Xmp
     */
    public function setCredit($credit)
    {
        $this->setAttr('photoshop:Credit', $credit, self::PHOTOSHOP_NS);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPhotographerTitle()
    {
        return $this->get('photoshop:AuthorsPosition', self::PHOTOSHOP_NS);
    }

    /**
     * @param $photographerTitle string
     *
     * @return Xmp
     */
    public function setPhotographerTitle($photographerTitle)
    {
        $this->setAttr('photoshop:AuthorsPosition', $photographerTitle, self::PHOTOSHOP_NS);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
        return $this->get('photoshop:Source', self::PHOTOSHOP_NS);
    }

    /**
     * @param $source string
     *
     * @return Xmp
     */
    public function setSource($source)
    {
        $this->setAttr('photoshop:Source', $source, self::PHOTOSHOP_NS);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCopyright()
    {
        return $this->getAlt('dc:rights', self::DC_NS);
    }

    /**
     * @param $copyright string
     *
     * @return Xmp
     */
    public function setCopyright($copyright)
    {
        $this->setAlt('dc:rights', $copyright, self::DC_NS);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCopyrightUrl()
    {
        return $this->get('xmpRights:WebStatement', self::XMP_RIGHTS_NS);
    }

    /**
     * @param $copyrightUrl string
     *
     * @return Xmp
     */
    public function setCopyrightUrl($copyrightUrl)
    {
        $this->setAttr('xmpRights:WebStatement', $copyrightUrl, self::XMP_RIGHTS_NS);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRightsUsageTerms()
    {
        return $this->getAlt('xmpRights:UsageTerms', self::XMP_RIGHTS_NS);
    }

    /**
     * @param $rightsUsageTerms string
     *
     * @return Xmp
     */
    public function setRightsUsageTerms($rightsUsageTerms)
    {
        $this->setAlt('xmpRights:UsageTerms', $rightsUsageTerms, self::XMP_RIGHTS_NS);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectName()
    {
        return $this->get('dc:title', self::DC_NS);
    }

    /**
     * @param $objectName string
     *
     * @return Xmp
     */
    public function setObjectName($objectName)
    {
        $this->setAlt('dc:title', $objectName, self::DC_NS);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptionWriters()
    {
        return $this->get('photoshop:CaptionWriter', self::PHOTOSHOP_NS);
    }

    /**
     * @param $captionWriters string
     *
     * @return Xmp
     */
    public function setCaptionWriters($captionWriters)
    {
        $this->setAttr('photoshop:CaptionWriter', $captionWriters, self::PHOTOSHOP_NS);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstructions()
    {
        return $this->get('photoshop:Instructions', self::PHOTOSHOP_NS);
    }

    /**
     * @param $instructions string
     *
     * @return Xmp
     */
    public function setInstructions($instructions)
    {
        $this->setAttr('photoshop:Instructions', $instructions, self::PHOTOSHOP_NS);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategory()
    {
        return $this->get('photoshop:Category', self::PHOTOSHOP_NS);
    }

    /**
     * @param $category string
     *
     * @return Xmp
     */
    public function setCategory($category)
    {
        $this->setAttr('photoshop:Category', $category, self::PHOTOSHOP_NS);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSupplementalCategories()
    {
        return $this->getBag('photoshop:SupplementalCategories', self::PHOTOSHOP_NS);
    }

    /**
     * @param $supplementalCategories array
     *
     * @return Xmp
     */
    public function setSupplementalCategories($supplementalCategories)
    {
        $this->setBag('photoshop:SupplementalCategories', $supplementalCategories, self::PHOTOSHOP_NS);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContactAddress()
    {
        return $this->getContactInfo('Iptc4xmpCore:CiAdrExtadr');
    }

    /**
     * @param $contactAddress string
     *
     * @return Xmp
     */
    public function setContactAddress($contactAddress)
    {
        $this->setContactAttr('Iptc4xmpCore:CiAdrExtadr', $contactAddress);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContactCity()
    {
        return $this->getContactInfo('Iptc4xmpCore:CiAdrCity');
    }

    /**
     * @param $contactCity string
     *
     * @return Xmp
     */
    public function setContactCity($contactCity)
    {
        $this->setContactAttr('Iptc4xmpCore:CiAdrCity', $contactCity);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContactState()
    {
        return $this->getContactInfo('Iptc4xmpCore:CiAdrRegion');
    }

    /**
     * @param $contactState string
     *
     * @return Xmp
     */
    public function setContactState($contactState)
    {
        $this->setContactAttr('Iptc4xmpCore:CiAdrRegion', $contactState);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContactZip()
    {
        return $this->getContactInfo('Iptc4xmpCore:CiAdrPcode');
    }

    /**
     * @param $contactZip string
     *
     * @return Xmp
     */
    public function setContactZip($contactZip)
    {
        $this->setContactAttr('Iptc4xmpCore:CiAdrPcode', $contactZip);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContactCountry()
    {
        return $this->getContactInfo('Iptc4xmpCore:CiAdrCtry');
    }

    /**
     * @param $contactCountry string
     *
     * @return Xmp
     */
    public function setContactCountry($contactCountry)
    {
        $this->setContactAttr('Iptc4xmpCore:CiAdrCtry', $contactCountry);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContactEmail()
    {
        return $this->getContactInfo('Iptc4xmpCore:CiEmailWork');
    }

    /**
     * @param $field
     *
     * @return null|string
     */
    private function getContactInfo($field)
    {
        $contactInfo = $this->getNode('Iptc4xmpCore:CreatorContactInfo', self::IPTC4_XMP_CORE_NS);

        if (!$contactInfo) {
            return null;
        }

        $email = $this->xpath->query($field . '|@' . $field, $contactInfo);

        if ($email->length) {
            return $email->item(0)->nodeValue;
        }

        return null;
    }

    /**
     * @param $contactEmail string
     *
     * @return Xmp
     */
    public function setContactEmail($contactEmail)
    {
        $this->setContactAttr('Iptc4xmpCore:CiEmailWork', $contactEmail);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContactPhone()
    {
        return $this->getContactInfo('Iptc4xmpCore:CiTelWork');
    }

    /**
     * @param $contactPhone string
     *
     * @return Xmp
     */
    public function setContactPhone($contactPhone)
    {
        $this->setContactAttr('Iptc4xmpCore:CiTelWork', $contactPhone);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContactUrl()
    {
        return $this->getContactInfo('Iptc4xmpCore:CiUrlWork');
    }

    /**
     * @param $contactUrl string
     *
     * @return Xmp
     */
    public function setContactUrl($contactUrl)
    {
        $this->setContactAttr('Iptc4xmpCore:CiUrlWork', $contactUrl);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getKeywords()
    {
        return $this->getBag('dc:subject', self::DC_NS);
    }

    /**
     * @param $keywords array
     *
     * @return Xmp
     */
    public function setKeywords($keywords)
    {
        $this->setBag('dc:subject', $keywords, self::DC_NS);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransmissionReference()
    {
        return $this->get('photoshop:TransmissionReference', self::PHOTOSHOP_NS);
    }

    /**
     * @param $transmissionReference string
     *
     * @return Xmp
     */
    public function setTransmissionReference($transmissionReference)
    {
        $this->setAttr('photoshop:TransmissionReference', $transmissionReference, self::PHOTOSHOP_NS);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrgency()
    {
        return $this->get('photoshop:Urgency', self::PHOTOSHOP_NS);
    }

    /**
     * @param $urgency string
     *
     * @return Xmp
     */
    public function setUrgency($urgency)
    {
        $this->setAttr('photoshop:Urgency', $urgency, self::PHOTOSHOP_NS);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRating()
    {
        return $this->get('xmp:Rating', self::XMP_NS);
    }

    /**
     * @param $rating
     *
     * @return Xmp
     */
    public function setRating($rating)
    {
        $this->setAttr('xmp:Rating', $rating, self::XMP_NS);
        return $this;
    }

    /**
     * @return array
     */
    public function getPersonsShown()
    {
        return $this->getBag('Iptc4xmpExt:PersonInImage', self::IPTC4_XMP_EXT_NS);
    }

    /**
     * @param array $personsShown
     *
     * @return Xmp
     */
    public function setPersonsShown($personsShown)
    {
        $this->setBag('Iptc4xmpExt:PersonInImage', $personsShown, self::IPTC4_XMP_EXT_NS);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIntellectualGenre()
    {
        return $this->get('Iptc4xmpCore:IntellectualGenre', self::IPTC4_XMP_CORE_NS);
    }

    /**
     * @param $intellectualGenre string
     *
     * @return Xmp
     */
    public function setIntellectualGenre($intellectualGenre)
    {
        $this->setAttr('Iptc4xmpCore:IntellectualGenre', $intellectualGenre, self::IPTC4_XMP_CORE_NS);
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateCreated()
    {
        $date = $this->get('photoshop:DateCreated', self::PHOTOSHOP_NS);

        if (!$date) {
            return null;
        }

        try {
            switch (strlen($date)) {
                case 4: // YYYY
                    return \DateTime::createFromFormat('Y', $date);
                case 7: // YYYY-MM
                    return \DateTime::createFromFormat('Y-m', $date);
                case 10: // YYYY-MM-DD
                    return \DateTime::createFromFormat('Y-m-d', $date);
                default:
                    return \DateTime::createFromFormat('c', $date);
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param \DateTime $dateCreated
     * @param string    $format
     *
     * @return Xmp
     */
    public function setDateCreated(\DateTime $dateCreated, $format = 'c')
    {
        $this->setAttr('photoshop:DateCreated', $dateCreated->format($format), self::PHOTOSHOP_NS);
        return $this;
    }

    /**
     * Get about.
     *
     * @return string
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * According to the XMP spec, the value of this attribute is required but should generally be empty.
     *
     * @param $about
     *
     * @return Xmp
     */
    public function setAbout($about)
    {
        $this->about = $about;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getToolkit()
    {
        $toolkit = $this->xpath->query('@x:xmptk', $this->dom->documentElement)->item(0);

        if ($toolkit) {
            return $toolkit->nodeValue;
        }

        return null;
    }

    /**
     * @param $toolkit
     *
     * @return $this
     */
    public function setToolkit($toolkit)
    {
        $this->dom->documentElement->setAttributeNS('adobe:ns:meta/', 'x:xmptk', $toolkit);
        return $this;
    }

    /**
     * @return string
     */
    public function getXml()
    {
        // ensure the xml has the required xpacket processing instructions
        $result = $this->xpath->query('/processing-instruction(\'xpacket\')');
        $hasBegin = $hasEnd = false;

        /** @var $item \DOMProcessingInstruction */
        foreach ($result as $item) {
            // do a quick check if the processing instruction contains 'begin' or 'end'
            if (strpos($item->nodeValue, 'begin') !== false) {
                $hasBegin = true;
            } elseif (strpos($item->nodeValue, 'end') !== false) {
                $hasEnd = true;
            }
        }

        if (!$hasBegin) {
            $this->dom->insertBefore(
                $this->dom->createProcessingInstruction(
                    'xpacket',
                    "begin=\"\xef\xbb\xbf\" id=\"W5M0MpCehiHzreSzNTczkc9d\""
                ),
                $this->dom->documentElement // insert before root
            );
        }

        if (!$hasEnd) {
            $this->dom->appendChild($this->dom->createProcessingInstruction('xpacket', 'end="w"')); // append to end
        }

        // ensure all rdf:Description elements have an rdf:about attribute
        $descriptions = $this->xpath->query('//rdf:Description');

        for ($i = 0; $i < $descriptions->length; $i++) {
            /** @var \DOMElement $desc */
            $desc = $descriptions->item($i);
            $desc->setAttributeNS(self::RDF_NS, 'rdf:about', $this->about);
        }

        // checks complete, return xml as string
        return $this->dom->saveXML();
    }

    /**
     * Get IPTC scene
     *
     * @return string
     */
    public function getIPTCScene()
    {
        return $this->getBag('Iptc4xmpCore:Scene', self::IPTC4_XMP_CORE_NS);
    }

    /**
     * Set IPTC scene
     *
     * @param $iptcScene
     *
     * @return $this
     */
    public function setIPTCScene($iptcScene)
    {
        $this->setBag('Iptc4xmpCore:Scene', $iptcScene, self::IPTC4_XMP_CORE_NS);
        return $this;
    }

    /**
     * Get featured organisation name
     *
     * @return string
     */
    public function getFeaturedOrganisationName()
    {
        return $this->getBag('Iptc4xmpExt:OrganisationInImageName', self::IPTC4_XMP_EXT_NS);
    }

    /**
     * Set featured organisation name.
     *
     * @param $featuredOrganisationName
     *
     * @return $this
     */
    public function setFeaturedOrganisationName($featuredOrganisationName)
    {
        $this->setBag('Iptc4xmpExt:OrganisationInImageName', $featuredOrganisationName, self::IPTC4_XMP_EXT_NS);
        return $this;
    }

    /**
     * Get featured organisation code
     *
     * @return string
     */
    public function getFeaturedOrganisationCode()
    {
        return $this->getBag('Iptc4xmpExt:OrganisationInImageCode', self::IPTC4_XMP_EXT_NS);
    }

    /**
     * Set featured organisation code
     *
     * @param $featuredOrganisationCode
     *
     * @return $this
     */
    public function setFeaturedOrganisationCode($featuredOrganisationCode)
    {
        $this->setBag('Iptc4xmpExt:OrganisationInImageCode', $featuredOrganisationCode, self::IPTC4_XMP_EXT_NS);
        return $this;
    }
}
