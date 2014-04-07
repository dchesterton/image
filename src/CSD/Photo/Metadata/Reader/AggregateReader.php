<?php
namespace CSD\Photo\Metadata\Reader;

use CSD\Photo\Metadata\Xmp;
use CSD\Photo\Metadata\JPEG;

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
     * @var IptcReader
     */
    private $iptc;

    /**
     * @var ExifReader
     */
    private $exif;

    /**
     * @var JPEG
     */
    private $jpeg;

    /**
     * @param JPEG $jpeg
     */
    public function __construct(JPEG $jpeg)
    {
        $this->jpeg = $jpeg;
    }

    /**
     * @return Xmp
     */
    public function getXmp()
    {
        if (!$this->xmp) {
            $this->xmp = Xmp::fromJPEG($this->jpeg);
        }
        return $this->xmp;
    }

    /**
     * @return IptcReader
     */
    public function getIptc()
    {
        if (!$this->iptc) {
            $this->iptc = IptcReader::fromJPEG($this->jpeg);
        }
        return $this->iptc;
    }

    /**
     * @return ExifReader
     */
    public function getExif()
    {
        if (!$this->iptc) {
            $this->exif = ExifReader::fromJPEG($this->jpeg);
        }
        return $this->iptc;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeadline()
    {
        return $this->getXmp()->getHeadline()?: $this->getIptc()->getHeadline();
    }

    /**
     * {@inheritdoc}
     */
    public function getKw()
    {
        return $this->getXmp()->getHeadline()?: $this->getIptc()->getHeadline();
    }

    /**
     * {@inheritdoc}
     */
    public function getCaption()
    {
        return $this->getXmp()->getCaption()?: $this->getIptc()->getCaption();
    }

    /**
     * {@inheritdoc}
     */
    public function getEvent()
    {
        return $this->getXmp()->getEvent()?: $this->getIptc()->getEvent();
    }

    /**
     * {@inheritdoc}
     */
    public function getLocation()
    {
        return $this->getXmp()->getLocation()?: $this->getIptc()->getLocation();
    }

    /**
     * {@inheritdoc}
     */
    public function getCity()
    {
        return $this->getXmp()->getCity()?: $this->getIptc()->getCity();
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return $this->getXmp()->getState()?: $this->getIptc()->getState();
    }

    /**
     * {@inheritdoc}
     */
    public function getCountry()
    {
        return $this->getXmp()->getCountry()?: $this->getIptc()->getCountry();
    }

    /**
     * {@inheritdoc}
     */
    public function getCountryCode()
    {
        return $this->getXmp()->getCountryCode()?: $this->getIptc()->getCountryCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getPhotographerName()
    {
        return $this->getXmp()->getPhotographerName()?: $this->getIptc()->getPhotographerName();
    }

    /**
     * {@inheritdoc}
     */
    public function getCredit()
    {
        return $this->getXmp()->getCredit()?: $this->getIptc()->getCredit();
    }

    /**
     * {@inheritdoc}
     */
    public function getPhotographerTitle()
    {
        return $this->getXmp()->getPhotographerTitle()?: $this->getIptc()->getPhotographerTitle();
    }

    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
        return $this->getXmp()->getSource()?: $this->getIptc()->getSource();
    }

    /**
     * {@inheritdoc}
     */
    public function getCopyright()
    {
        return $this->getXmp()->getCopyright()?: $this->getIptc()->getCopyright();
    }

    /**
     * {@inheritdoc}
     */
    public function getCopyrightUrl()
    {
        return $this->getXmp()->getCopyrightUrl()?: $this->getIptc()->getCopyrightUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function getRightsUsageTerms()
    {
        return $this->getXmp()->getRightsUsageTerms()?: $this->getIptc()->getRightsUsageTerms();
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectName()
    {
        return $this->getXmp()->getObjectName()?: $this->getIptc()->getObjectName();
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptionWriters()
    {
        return $this->getXmp()->getCaptionWriters()?: $this->getIptc()->getCaptionWriters();
    }

    /**
     * {@inheritdoc}
     */
    public function getInstructions()
    {
        return $this->getXmp()->getInstructions()?: $this->getIptc()->getInstructions();
    }

    /**
     * {@inheritdoc}
     */
    public function getCategory()
    {
        return $this->getXmp()->getCategory()?: $this->getIptc()->getCategory();
    }

    /**
     * {@inheritdoc}
     */
    public function getSupplementalCategories()
    {
        return $this->getXmp()->getSupplementalCategories()?: $this->getIptc()->getSupplementalCategories();
    }

    /**
     * {@inheritdoc}
     */
    public function getContactAddress()
    {
        return $this->getXmp()->getContactAddress()?: $this->getIptc()->getContactAddress();
    }

    /**
     * {@inheritdoc}
     */
    public function getContactCity()
    {
        return $this->getXmp()->getContactCity()?: $this->getIptc()->getContactCity();
    }

    /**
     * {@inheritdoc}
     */
    public function getContactState()
    {
        return $this->getXmp()->getContactState()?: $this->getIptc()->getContactState();
    }

    /**
     * {@inheritdoc}
     */
    public function getContactZip()
    {
        return $this->getXmp()->getContactZip()?: $this->getIptc()->getContactZip();
    }

    /**
     * {@inheritdoc}
     */
    public function getContactCountry()
    {
        return $this->getXmp()->getContactCountry()?: $this->getIptc()->getContactCountry();
    }

    /**
     * {@inheritdoc}
     */
    public function getContactEmail()
    {
        return $this->getXmp()->getContactEmail()?: $this->getIptc()->getContactEmail();
    }

    /**
     * {@inheritdoc}
     */
    public function getContactPhone()
    {
        return $this->getXmp()->getContactPhone()?: $this->getIptc()->getContactPhone();
    }

    /**
     * {@inheritdoc}
     */
    public function getContactUrl()
    {
        return $this->getXmp()->getContactUrl()?: $this->getIptc()->getContactUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function getTransmissionReference()
    {
        return $this->getXmp()->getTransmissionReference()?: $this->getIptc()->getTransmissionReference();
    }

    /**
     * {@inheritdoc}
     */
    public function getUrgency()
    {
        return $this->getXmp()->getUrgency()?: $this->getIptc()->getUrgency();
    }

    /**
     * {@inheritdoc}
     */
    public function getRating()
    {
        return $this->getXmp()->getRating()?: $this->getIptc()->getRating();
    }

    /**
     * {@inheritdoc}
     */
    public function getKeywords()
    {
        return $this->getXmp()->getKeywords()?: $this->getIptc()->getKeywords();
    }

    /**
     * {@inheritdoc}
     */
    public function getIPTCSubjectCodes()
    {
        return $this->getXmp()->getIPTCSubjectCodes()?: $this->getIptc()->getIPTCSubjectCodes();
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
        return $this->getXmp()->getIntellectualGenre()?: $this->getIptc()->getIntellectualGenre();
    }

    /**
     * Get featured organisation name
     *
     * @return string
     */
    public function getFeaturedOrganisationName()
    {
        return $this->getXmp()->getFeaturedOrganisationName()?: $this->getIptc()->getFeaturedOrganisationName();
    }

    /**
     * Get featured organisation code
     *
     * @return string
     */
    public function getFeaturedOrganisationCode()
    {
        return $this->getXmp()->getFeaturedOrganisationCode()?: $this->getIptc()->getFeaturedOrganisationCode();
    }

    /**
     * Get IPTC scene
     *
     * @return string
     */
    public function getIPTCScene()
    {
        return $this->getXmp()->getIPTCScene()?: $this->getIptc()->getIPTCScene();
    }
}
