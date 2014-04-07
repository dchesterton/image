<?php
namespace CSD\Photo\Metadata\Reader;

/**
 *
 */
interface MetadataReaderInterface
{
    /**
     * Get headline
     *
     * @return string
     */
    public function getHeadline();

    /**
     * Get caption
     *
     * @return string
     */
    public function getCaption();

    /**
     * Get event
     *
     * @return string
     */
    public function getEvent();

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation();

    /**
     * Get city
     *
     * @return string
     */
    public function getCity();

    /**
     * Get state
     *
     * @return string
     */
    public function getState();

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry();

    /**
     * Get countryCode
     *
     * @return string
     */
    public function getCountryCode();

    /**
     * Get IPTC subject codes
     *
     * @return array|null
     */
    public function getIPTCSubjectCodes();

    /**
     * Get IPTC scene
     * @return string
     */
    public function getIPTCScene();

    /**
     * Get photographerName
     *
     * @return string
     */
    public function getPhotographerName();

    /**
     * Get credit
     *
     * @return string
     */
    public function getCredit();

    /**
     * Get photographerTitle
     *
     * @return string
     */
    public function getPhotographerTitle();

    /**
     * Get source
     *
     * @return string
     */
    public function getSource();

    /**
     * Get copyright
     *
     * @return string
     */
    public function getCopyright();

    /**
     * Get copyrightUrl
     *
     * @return string
     */
    public function getCopyrightUrl();

    /**
     * Get rightsUsageTerms
     *
     * @return string
     */
    public function getRightsUsageTerms();

    /**
     * Get objectName
     *
     * @return string
     */
    public function getObjectName();

    /**
     * Get captionWriters
     *
     * @return string
     */
    public function getCaptionWriters();

    /**
     * Get instructions
     *
     * @return string
     */
    public function getInstructions();

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory();

    /**
     * Get supplementalCategories
     *
     * @return string
     */
    public function getSupplementalCategories();

    /**
     * Get contactAddress
     *
     * @return string
     */
    public function getContactAddress();

    /**
     * Get contactCity
     *
     * @return string
     */
    public function getContactCity();

    /**
     * Get contactState
     *
     * @return string
     */
    public function getContactState();

    /**
     * Get contactZip
     *
     * @return string
     */
    public function getContactZip();

    /**
     * Get contactCountry
     *
     * @return string
     */
    public function getContactCountry();

    /**
     * Get contactEmail
     *
     * @return string
     */
    public function getContactEmail();

    /**
     * Get contactPhone
     *
     * @return string
     */
    public function getContactPhone();

    /**
     * Get contactUrl
     *
     * @return string
     */
    public function getContactUrl();

    /**
     * Get transmissionReference
     *
     * @return string
     */
    public function getTransmissionReference();

    /**
     * Get urgency
     *
     * @return string
     */
    public function getUrgency();

    /**
     * Get rating
     *
     * @return string
     */
    public function getRating();

    /**
     * Get keywords
     *
     * @return string
     */
    public function getKeywords();

    /**
     * Get intellectual genre
     *
     * @return string
     */
    public function getIntellectualGenre();

    /**
     * Get featured organisation name
     *
     * @return string
     */
    public function getFeaturedOrganisationName();

    /**
     * Get featured organisation code
     *
     * @return string
     */
    public function getFeaturedOrganisationCode();
}
