<?php
namespace CSD\Photo;

interface Captionable
{
    /**
     * Set the caption body text.
     *
     * @param string $caption
     *
     * @return Captionable
     */
    public function setCaption($caption);

    /**
     * Get the caption body text.
     *
     * @return string
     */
    public function getCaption();

    /**
     * Set caption writer(s).
     *
     * @param string $captionWriters
     *
     * @return Captionable
     */
    public function setCaptionWriters($captionWriters);

    /**
     * Get caption writer(s).
     *
     * @return string
     */
    public function getCaptionWriters();

    /**
     * Set category.
     *
     * @param string $category
     *
     * @return Captionable
     */
    public function setCategory($category);

    /**
     * Get category.
     *
     * @return string
     */
    public function getCategory();

    /**
     * Set city.
     *
     * @param string $city
     *
     * @return Captionable
     */
    public function setCity($city);

    /**
     * Get city.
     *
     * @return string
     */
    public function getCity();

    /**
     * Set contactAddress.
     *
     * @param string $contactAddress
     *
     * @return Captionable
     */
    public function setContactAddress($contactAddress);

    /**
     * Get contactAddress.
     *
     * @return string
     */
    public function getContactAddress();

    /**
     * Set contactCity.
     *
     * @param string $contactCity
     *
     * @return Captionable
     */
    public function setContactCity($contactCity);

    /**
     * Get contactCity.
     *
     * @return string
     */
    public function getContactCity();

    /**
     * Set contactCountry.
     *
     * @param string $contactCountry
     *
     * @return Captionable
     */
    public function setContactCountry($contactCountry);

    /**
     * Get contactCountry.
     *
     * @return string
     */
    public function getContactCountry();

    /**
     * Set contactEmail.
     *
     * @param string $contactEmail
     *
     * @return Captionable
     */
    public function setContactEmail($contactEmail);

    /**
     * Get contactEmail.
     *
     * @return string
     */
    public function getContactEmail();

    /**
     * Set contactPhone.
     *
     * @param string $contactPhone
     *
     * @return Captionable
     */
    public function setContactPhone($contactPhone);

    /**
     * Get contactPhone.
     *
     * @return string
     */
    public function getContactPhone();

    /**
     * Set contactState.
     *
     * @param string $contactState
     *
     * @return Captionable
     */
    public function setContactState($contactState);

    /**
     * Get contactState.
     *
     * @return string
     */
    public function getContactState();

    /**
     * Set contactUrl.
     *
     * @param string $contactUrl
     *
     * @return Captionable
     */
    public function setContactUrl($contactUrl);

    /**
     * Get contactUrl.
     *
     * @return string
     */
    public function getContactUrl();

    /**
     * Set contactZip.
     *
     * @param string $contactZip
     *
     * @return Captionable
     */
    public function setContactZip($contactZip);

    /**
     * Get contactZip.
     *
     * @return string
     */
    public function getContactZip();

    /**
     * Set copyright.
     *
     * @param string $copyright
     *
     * @return Captionable
     */
    public function setCopyright($copyright);

    /**
     * Get copyright.
     *
     * @return string
     */
    public function getCopyright();

    /**
     * Set copyrightUrl.
     *
     * @param string $copyrightUrl
     *
     * @return Captionable
     */
    public function setCopyrightUrl($copyrightUrl);

    /**
     * Get copyrightUrl.
     *
     * @return string
     */
    public function getCopyrightUrl();

    /**
     * Set country.
     *
     * @param string $country
     *
     * @return Captionable
     */
    public function setCountry($country);

    /**
     * Get country.
     *
     * @return string
     */
    public function getCountry();

    /**
     * Set country code.
     *
     * @param string $countryCode
     *
     * @return Captionable
     */
    public function setCountryCode($countryCode);

    /**
     * Get country code.
     *
     * @return string
     */
    public function getCountryCode();

    /**
     * Set credit.
     *
     * @param string $credit
     *
     * @return Captionable
     */
    public function setCredit($credit);

    /**
     * Get credit.
     *
     * @return string
     */
    public function getCredit();

    /**
     * Set event.
     *
     * @param string $event
     *
     * @return Captionable
     */
    public function setEvent($event);

    /**
     * Get event.
     *
     * @return string
     */
    public function getEvent();

    /**
     * Set headline.
     *
     * @param string $headline
     *
     * @return Captionable
     */
    public function setHeadline($headline);

    /**
     * Get headline.
     *
     * @return string
     */
    public function getHeadline();

    /**
     * Set instructions.
     *
     * @param string $instructions
     *
     * @return Captionable
     */
    public function setInstructions($instructions);

    /**
     * Get instructions.
     *
     * @return string
     */
    public function getInstructions();

    /**
     * Set location.
     *
     * @param string $location
     *
     * @return Captionable
     */
    public function setLocation($location);

    /**
     * Get location.
     *
     * @return string
     */
    public function getLocation();

    /**
     * Set objectName.
     *
     * @param string $objectName
     *
     * @return Captionable
     */
    public function setObjectName($objectName);

    /**
     * Get objectName.
     *
     * @return string
     */
    public function getObjectName();

    /**
     * Set photographer name.
     *
     * @param string $photographerName
     *
     * @return Captionable
     */
    public function setPhotographerName($photographerName);

    /**
     * Get photographer name.
     *
     * @return string
     */
    public function getPhotographerName();

    /**
     * Set photographerTitle.
     *
     * @param string $photographerTitle
     *
     * @return Captionable
     */
    public function setPhotographerTitle($photographerTitle);

    /**
     * Get photographer title in organisation.
     *
     * @return string
     */
    public function getPhotographerTitle();

    /**
     * Set rights usage terms.
     *
     * @param string $rightsUsageTerms
     *
     * @return Captionable
     */
    public function setRightsUsageTerms($rightsUsageTerms);

    /**
     * Get rights usage terms.
     *
     * @return string
     */
    public function getRightsUsageTerms();

    /**
     * Set source.
     *
     * @param string $source
     *
     * @return Captionable
     */
    public function setSource($source);

    /**
     * Get source.
     *
     * @return string
     */
    public function getSource();

    /**
     * Set state/region.
     *
     * @param string $state
     *
     * @return Captionable
     */
    public function setState($state);

    /**
     * Get state/region.
     *
     * @return string
     */
    public function getState();

    /**
     * Set the IPTC subject codes for the photo.
     *
     * @param string $iptcSubjectCodes
     *
     * @return Captionable
     */
    public function setIptcSubjectCodes($iptcSubjectCodes);

    /**
     * Get the IPTC subject codes for the photo.
     *
     * @return array
     */
    public function getIptcSubjectCodes();

    /**
     * Get iptcScene.
     *
     * @return string
     */
    public function getIptcScene();

    /**
     * Set iptcScene.
     *
     * @param string $iptcScene
     *
     * @return Captionable
     */
    public function setIptcScene($iptcScene);

    /**
     * Set supplemental categories.
     *
     * @param string $supplementalCategories
     *
     * @return Captionable
     */
    public function setSupplementalCategories($supplementalCategories);

    /**
     * Get supplemental categories.
     *
     * @return string
     */
    public function getSupplementalCategories();

    /**
     * Get featuredOrganisationName.
     *
     * @return string
     */
    public function getFeaturedOrganisationName();

    /**
     * Set featuredOrganisationName.
     *
     * @param string $featuredOrganisationName
     *
     * @return Captionable
     */
    public function setFeaturedOrganisationName($featuredOrganisationName);

    /**
     * Get featuredOrganisationCode.
     *
     * @return string
     */
    public function getFeaturedOrganisationCode();

    /**
     * Set featuredOrganisationCode.
     *
     * @param string $featuredOrganisationCode
     *
     * @return Captionable
     */
    public function setFeaturedOrganisationCode($featuredOrganisationCode);

    /**
     * Get intellectualGenre.
     *
     * @return string
     */
    public function getIntellectualGenre();

    /**
     * Set intellectualGenre.
     *
     * @param string $intellectualGenre
     *
     * @return Captionable
     */
    public function setIntellectualGenre($intellectualGenre);

    /**
     * Set urgency.
     *
     * @param integer $urgency
     *
     * @return Captionable
     */
    public function setUrgency($urgency);

    /**
     * Get urgency.
     *
     * @return integer
     */
    public function getUrgency();

    /**
     * Set rating.
     *
     * @param integer $rating
     *
     * @return Captionable
     */
    public function setRating($rating);

    /**
     * Get rating.
     *
     * @return integer
     */
    public function getRating();
}
