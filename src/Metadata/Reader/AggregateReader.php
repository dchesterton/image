<?php
namespace CSD\Photo\Metadata\Reader;

use CSD\Photo\Metadata\Exif;
use CSD\Photo\Metadata\Iptc;
use CSD\Photo\Metadata\Xmp;

/**
 * Aggregate metadata reader. Uses XMP to get metadata, falls back to IPTC where available.
 */
class AggregateReader implements MetadataReaderInterface
{
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
     *
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
     * @param array  $supported
     *
     * @return string|null
     */
    private function getMeta($field, $supported = null)
    {
        $value = null;

        foreach ($this->priority as $metaType) {
            // check if this meta type is supported for this field
            if (!in_array($metaType, $supported, true)) {
                continue;
            }

            $metaObject = $this->$metaType;

            if (!$metaObject) {
                // meta type object not available
                continue;
            }

            $method = 'get' . ucfirst($field);

            $value = $metaObject->$method();

            if ($value) {
                break;
            }
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeadline()
    {
        return $this->getMeta('headline', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getCaption()
    {
        return $this->getMeta('Caption', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getEvent()
    {
        return $this->getMeta('Event', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getLocation()
    {
        return $this->getMeta('Location', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getCity()
    {
        return $this->getMeta('City', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return $this->getMeta('State', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getCountry()
    {
        return $this->getMeta('Country', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getCountryCode()
    {
        return $this->getMeta('CountryCode', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getPhotographerName()
    {
        return $this->getMeta('PhotographerName', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getCredit()
    {
        return $this->getMeta('Credit', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getPhotographerTitle()
    {
        return $this->getMeta('PhotographerTitle', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
        return $this->getMeta('Source', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getCopyright()
    {
        return $this->getMeta('Copyright', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getCopyrightUrl()
    {
        return $this->getMeta('CopyrightUrl', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getRightsUsageTerms()
    {
        return $this->getMeta('RightsUsageTerms', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectName()
    {
        return $this->getMeta('ObjectName', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptionWriters()
    {
        return $this->getMeta('CaptionWriters', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getInstructions()
    {
        return $this->getMeta('Instructions', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getCategory()
    {
        return $this->getMeta('Category', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getSupplementalCategories()
    {
        return $this->getMeta('SupplementalCategories', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getContactAddress()
    {
        return $this->getMeta('ContactAddress', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getContactCity()
    {
        return $this->getMeta('ContactCity', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getContactState()
    {
        return $this->getMeta('ContactState', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getContactZip()
    {
        return $this->getMeta('ContactZip', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getContactCountry()
    {
        return $this->getMeta('ContactCountry', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getContactEmail()
    {
        return $this->getMeta('ContactEmail', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getContactPhone()
    {
        return $this->getMeta('ContactPhone', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getContactUrl()
    {
        return $this->getMeta('ContactUrl', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getTransmissionReference()
    {
        return $this->getMeta('TransmissionReference', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrgency()
    {
        return $this->getMeta('Urgency', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getRating()
    {
        return $this->getMeta('Rating', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getKeywords()
    {
        return $this->getMeta('Keywords', ['xmp', 'iptc']);
    }

    /**
     * {@inheritdoc}
     */
    public function getIPTCSubjectCodes()
    {
        return $this->getMeta('IPTCSubjectCodes', ['xmp', 'iptc']);
    }

    /**
     *
     */
    public function getCreatedAt()
    {
        return new \DateTime('now');


        // get image date
        if ($iptc->has('2#055') && $iptc->has('2#060')) {
            $createdAt = new DateTime($iptc->get('2#055') . ' ' . $iptc->get('2#060'));
        } elseif ($xmp->get('photoshop:DateCreated')) {
            $createdAt = new DateTime($xmp->get('photoshop:DateCreated'));
        } else {
            $createdAt = new DateTime;
        }
    }

    /**
     * Get intellectual genre
     *
     * @return string
     */
    public function getIntellectualGenre()
    {
        return $this->getMeta('IntellectualGenre', ['xmp', 'iptc']);
    }

    /**
     * Get featured organisation name
     *
     * @return string
     */
    public function getFeaturedOrganisationName()
    {
        return $this->getMeta('FeaturedOrganisationName', ['xmp', 'iptc']);
    }

    /**
     * Get featured organisation code
     *
     * @return string
     */
    public function getFeaturedOrganisationCode()
    {
        return $this->getMeta('FeaturedOrganisationCode', ['xmp', 'iptc']);
    }

    /**
     * Get IPTC scene
     *
     * @return string
     */
    public function getIPTCScene()
    {
        return $this->getMeta('IPTCScene', ['xmp', 'iptc']);
    }
}
