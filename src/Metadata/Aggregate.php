<?php
namespace CSD\Photo\Metadata;

/**
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class Aggregate
{
    private $fields = [
        'headline' => ['xmp', 'iptc'],
        'caption' => ['xmp', 'iptc'],
        'location' => ['xmp', 'iptc'],
        'city' => ['xmp', 'iptc'],
        'state' => ['xmp', 'iptc'],
        'country' => ['xmp', 'iptc'],
        'countryCode' => ['xmp', 'iptc'],
        'photographerName' => ['xmp', 'iptc'],
        'credit' => ['xmp', 'iptc'],
        'photographerTitle' => ['xmp', 'iptc'],
        'source' => ['xmp', 'iptc'],
        'copyright' => ['xmp', 'iptc'],
        'objectName' => ['xmp', 'iptc'],
        'captionWriters' => ['xmp', 'iptc'],
        'instructions' => ['xmp', 'iptc'],
        'category' => ['xmp', 'iptc'],
        'supplementalCategories' => ['xmp', 'iptc'],
        'transmissionReference' => ['xmp', 'iptc'],
        'urgency' => ['xmp', 'iptc'],
        'keywords' => ['xmp', 'iptc'],
        'dateCreated' => ['xmp', 'iptc']
    ];

    /**
     * @var Xmp
     */
    private $xmp;

    /**
     * @var Iptc
     */
    private $iptc;

    /**
     * @var Exif
     */
    private $exif;

    /**
     * @var array
     */
    private $priority;

    /**
     * Constructor
     *
     * @param Xmp  $xmp
     * @param Iptc $iptc
     * @param Exif $exif
     */
    public function __construct(Xmp $xmp = null, Iptc $iptc = null, Exif $exif = null)
    {
        $this->xmp = $xmp;
        $this->iptc = $iptc;
        $this->exif = $exif;
        $this->priority = ['xmp', 'iptc', 'exif'];
    }

    /**
     * @param array $priority
     *
     * @return $this
     * @throws \Exception
     */
    public function setPriority(array $priority)
    {
        foreach ($priority as $metaType) {
            if (!in_array($metaType, ['xmp', 'iptc', 'exif'], true)) {
                throw new \Exception('Priority can only contain xmp, iptc or exif');
            }
        }

        $this->priority = $priority;
        return $this;
    }

    /**
     * @param string $field
     *
     * @return string|null
     */
    private function get($field)
    {
        foreach ($this->priority as $metaType) {
            // check if this meta type is supported for this field
            if (!in_array($metaType, $this->fields[$field], true)) {
                continue;
            }

            $metaObject = $this->$metaType;

            if (!$metaObject) {
                continue;
            }

            $getter = 'get' . ucfirst($field);
            $value = $metaObject->$getter();

            if ($value) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param $field
     * @param $value
     *
     * @return $this
     */
    private function set($field, $value)
    {
        $supported = $this->fields[$field];

        foreach ($supported as $metaType) {
            $metaObject = $this->$metaType;

            if (!$metaObject) {
                continue;
            }

            $setter = 'set' . ucfirst($field);
            $metaObject->$setter($value);
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHeadline()
    {
        return $this->get('headline');
    }

    /**
     * @param string $headline
     *
     * @return $this
     */
    public function setHeadline($headline)
    {
        return $this->set('headline', $headline);
    }

    /**
     * @return string|null
     */
    public function getCaption()
    {
        return $this->get('caption');
    }

    /**
     * @param string $caption
     *
     * @return $this
     */
    public function setCaption($caption)
    {
        return $this->set('caption', $caption);
    }

    /**
     * @return string|null
     */
    public function getLocation()
    {
        return $this->get('location');
    }

    /**
     * @param string $location
     *
     * @return $this
     */
    public function setLocation($location)
    {
        return $this->set('location', $location);
    }

    /**
     * @return string|null
     */
    public function getCity()
    {
        return $this->get('city');
    }

    /**
     * @param string $city
     *
     * @return $this
     */
    public function setCity($city)
    {
        return $this->set('city', $city);
    }

    /**
     * @return string|null
     */
    public function getState()
    {
        return $this->get('state');
    }

    /**
     * @param string $state
     *
     * @return $this
     */
    public function setState($state)
    {
        return $this->set('state', $state);
    }

    /**
     * @return string|null
     */
    public function getCountry()
    {
        return $this->get('country');
    }

    /**
     * @param string $country
     *
     * @return $this
     */
    public function setCountry($country)
    {
        return $this->set('country', $country);
    }

    /**
     * @return string|null
     */
    public function getCountryCode()
    {
        return $this->get('countryCode');
    }

    /**
     * @param string $countryCode
     *
     * @return $this
     */
    public function setCountryCode($countryCode)
    {
        return $this->set('countryCode', $countryCode);
    }

    /**
     * @return string|null
     */
    public function getPhotographerName()
    {
        return $this->get('photographerName');
    }

    /**
     * @param string $photographerName
     *
     * @return $this
     */
    public function setPhotographerName($photographerName)
    {
        return $this->set('photographerName', $photographerName);
    }

    /**
     * @return string|null
     */
    public function getCredit()
    {
        return $this->get('credit');
    }

    /**
     * @param string $credit
     *
     * @return $this
     */
    public function setCredit($credit)
    {
        return $this->set('credit', $credit);
    }

    /**
     * @return string|null
     */
    public function getPhotographerTitle()
    {
        return $this->get('photographerTitle');
    }

    /**
     * @param string $photographerTitle
     *
     * @return $this
     */
    public function setPhotographerTitle($photographerTitle)
    {
        return $this->set('photographerTitle', $photographerTitle);
    }

    /**
     * @return string|null
     */
    public function getSource()
    {
        return $this->get('source');
    }

    /**
     * @param string $source
     *
     * @return $this
     */
    public function setSource($source)
    {
        return $this->set('source', $source);
    }

    /**
     * @return string|null
     */
    public function getCopyright()
    {
        return $this->get('copyright');
    }

    /**
     * @param string $copyright
     *
     * @return $this
     */
    public function setCopyright($copyright)
    {
        return $this->set('copyright', $copyright);
    }

    /**
     * @return string|null
     */
    public function getObjectName()
    {
        return $this->get('objectName');
    }

    /**
     * @param string $objectName
     *
     * @return $this
     */
    public function setObjectName($objectName)
    {
        return $this->set('objectName', $objectName);
    }

    /**
     * @return string|null
     */
    public function getCaptionWriters()
    {
        return $this->get('captionWriters');
    }

    /**
     * @param string $captionWriters
     *
     * @return $this
     */
    public function setCaptionWriters($captionWriters)
    {
        return $this->set('captionWriters', $captionWriters);
    }

    /**
     * @return string|null
     */
    public function getInstructions()
    {
        return $this->get('instructions');
    }

    /**
     * @param string $instructions
     *
     * @return $this
     */
    public function setInstructions($instructions)
    {
        return $this->set('instructions', $instructions);
    }

    /**
     * @return string|null
     */
    public function getCategory()
    {
        return $this->get('category');
    }

    /**
     * @param string $category
     *
     * @return $this
     */
    public function setCategory($category)
    {
        return $this->set('category', $category);
    }

    /**
     * @return string|null
     */
    public function getSupplementalCategories()
    {
        return $this->get('supplementalCategories');
    }

    /**
     * @param string $supplementalCategories
     *
     * @return $this
     */
    public function setSupplementalCategories($supplementalCategories)
    {
        return $this->set('supplementalCategories', $supplementalCategories);
    }

    /**
     * @return string|null
     */
    public function getTransmissionReference()
    {
        return $this->get('transmissionReference');
    }

    /**
     * @param string $transmissionReference
     *
     * @return $this
     */
    public function setTransmissionReference($transmissionReference)
    {
        return $this->set('transmissionReference', $transmissionReference);
    }

    /**
     * @return string|null
     */
    public function getUrgency()
    {
        return $this->get('urgency');
    }

    /**
     * @param string $urgency
     *
     * @return $this
     */
    public function setUrgency($urgency)
    {
        return $this->set('urgency', $urgency);
    }

    /**
     * @return array|null
     */
    public function getKeywords()
    {
        return $this->get('keywords');
    }

    /**
     * @param mixed $keywords
     *
     * @return $this
     */
    public function setKeywords($keywords)
    {
        return $this->set('keywords', $keywords);
    }

    /**
     * @return \DateTime|null
     */
    public function getDateCreated()
    {
        return $this->get('dateCreated');
    }

    /**
     * @param \DateTime $dateCreated
     *
     * @return $this
     */
    public function setDateCreated(\DateTime $dateCreated)
    {
        return $this->set('dateCreated', $dateCreated);
    }
}
