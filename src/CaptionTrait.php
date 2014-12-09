<?php
namespace CSD\Photo;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Caption trait used to save the common IPTC/XMP meta data used throughout
 * the system. The trait is used by assignments, templates and photo entities.
 *
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
trait CaptionTrait
{
    /**
     * The photo headline.
     *
     * @var string
     * @ORM\Column(type="string", length=200, nullable=true)
     *
     * @Serializer\Expose
     */
    private $headline;

    /**
     * The caption body text.
     *
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @Serializer\Expose
     */
    private $caption;

    /**
     * The event the photo was taken at.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     *
     * @Serializer\Expose
     */
    private $event;

    /**
     * The location the photo was taken at.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     *
     * @Serializer\Expose
     */
    private $location;

    /**
     * The city where the photo was taken.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     *
     * @Serializer\Expose
     */
    private $city;

    /**
     * The state/region where the photo was taken.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     *
     * @Serializer\Expose
     */
    private $state;

    /**
     * The country where the photo was taken.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     *
     * @Serializer\Expose
     */
    private $country;

    /**
     * The country code for the country where the photo was taken.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=100, name="country_code", nullable=true)
     *
     * @Serializer\Expose
     */
    private $countryCode;

    /**
     * The IPTC subject codes for the photo.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=200, name="iptc_subject_codes", nullable=true)
     *
     * @Serializer\Expose
     */
    private $iptcSubjectCodes;

    /**
     * The IPTC scene codes for the photo.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=250, name="iptc_scene", nullable=true)
     *
     * @Serializer\Expose
     */
    private $iptcScene;

    /**
     * The photographer's name.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=200, name="photographer_name", nullable=true)
     *
     * @Serializer\Expose
     */
    private $photographerName;

    /**
     * The credit line to use when crediting the photo, e.g. in a newspaper byline.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     *
     * @Serializer\Expose
     */
    private $credit;

    /**
     * The photographer's title within the organisation, e.g. Stringer/Staff.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=200, name="photographer_title", nullable=true)
     *
     * @Serializer\Expose
     */
    private $photographerTitle;

    /**
     * The source of the photo.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     *
     * @Serializer\Expose
     */
    private $source;

    /**
     * The copyright holder for the photo.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     *
     * @Serializer\Expose
     */
    private $copyright;

    /**
     * A URL for the copyright holder of the photo.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=200, name="copyright_url", nullable=true)
     *
     * @Serializer\Expose
     */
    private $copyrightUrl;

    /**
     * Specific usage terms for the photo, used to specify any terms for publications such as restrictions and
     * embargoes, e.g. "Editorial use only, embargoed until 10am Friday 10th August GMT".
     *
     * @var string
     *
     * @ORM\Column(type="string", name="rights_usage_terms", nullable=true)
     *
     * @Serializer\Expose
     */
    private $rightsUsageTerms;

    /**
     * Object name for the photo.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=200, name="object_name", nullable=true)
     *
     * @Serializer\Expose
     */
    private $objectName;

    /**
     * The caption writer(s).
     *
     * @var string
     *
     * @ORM\Column(type="string", length=100, name="caption_writers", nullable=true)
     *
     * @Serializer\Expose
     */
    private $captionWriters;

    /**
     * Special instructions for the photo.
     *
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @Serializer\Expose
     */
    private $instructions;

    /**
     * The broad subject category for the photo. Replaced by {@see Caption::subjectCode}.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=3, nullable=true)
     *
     * @Serializer\Expose
     */
    private $category;

    /**
     * Additional category for the photo. Replaced by {@see Caption::subjectCode}.
     *
     * @var string
     *
     * @ORM\Column(type="text", name="supplemental_categories", nullable=true)
     *
     * @Serializer\Expose
     */
    private $supplementalCategories;

    /**
     * The address to which mail should be sent to contact the person or organisation that created this image.
     *
     * @var string
     *
     * @ORM\Column(type="text", name="contact_address", nullable=true)
     *
     * @Serializer\Expose
     */
    private $contactAddress;

    /**
     * The city in which the person or organisation that created this image is located.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, name="contact_city", nullable=true)
     *
     * @Serializer\Expose
     */
    private $contactCity;

    /**
     * The state/region in which the person or organisation that created this image is located.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, name="contact_state", nullable=true)
     *
     * @Serializer\Expose
     */
    private $contactState;

    /**
     * The zip code/postcode in which the person or organisation that created this image is located.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, name="contact_zip", nullable=true)
     *
     * @Serializer\Expose
     */
    private $contactZip;

    /**
     * The country in which the person or organisation that created this image is located.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, name="contact_country", nullable=true)
     *
     * @Serializer\Expose
     */
    private $contactCountry;

    /**
     * The email address for the person or organisation that created this image.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, name="contact_email", nullable=true)
     *
     * @Serializer\Expose
     */
    private $contactEmail;

    /**
     * The phone number for the person or organisation that created this image.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, name="contact_phone", nullable=true)
     *
     * @Serializer\Expose
     */
    private $contactPhone;

    /**
     * The website URL for the person or organisation that created this image.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=250, name="contact_url", nullable=true)
     *
     * @Serializer\Expose
     */
    private $contactUrl;

    /**
     * The name of the organisation featured in this image.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=250, name="featured_org_name", nullable=true)
     *
     * @Serializer\Expose
     */
    private $featuredOrganisationName;

    /**
     * The code of the organisation featured in this image.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=250, name="featured_org_code", nullable=true)
     *
     * @Serializer\Expose
     */
    private $featuredOrganisationCode;

    /**
     * The intellectual genre of this image.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=250, name="intellectual_genre", nullable=true)
     *
     * @Serializer\Expose
     */
    private $intellectualGenre;

    /**
     * The urgency of the photo.
     *
     * @var integer
     *
     * @ORM\Column(type="smallint", nullable=true, options={"unsigned" = true})
     *
     * @Serializer\Expose
     */
    private $urgency = 0;

    /**
     * The rating of the photo.
     *
     * @var integer
     *
     * @ORM\Column(type="smallint", nullable=true, options={"unsigned" = true})
     *
     * @Serializer\Expose
     */
    private $rating = 0;


    /**
     * Get the caption as a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->headline;
    }

    /**
     * Set the caption body text.
     *
     * @param string $caption
     *
     * @return CaptionTrait
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
        return $this;
    }

    /**
     * Get the caption body text.
     *
     * @return string
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Set caption writer(s).
     *
     * @param string $captionWriters
     *
     * @return CaptionTrait
     */
    public function setCaptionWriters($captionWriters)
    {
        $this->captionWriters = $captionWriters;
        return $this;
    }

    /**
     * Get caption writer(s).
     *
     * @return string
     */
    public function getCaptionWriters()
    {
        return $this->captionWriters;
    }

    /**
     * Set category.
     *
     * @param string $category
     *
     * @return CaptionTrait
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category.
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set city.
     *
     * @param string $city
     *
     * @return CaptionTrait
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Get city.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set contactAddress.
     *
     * @param string $contactAddress
     *
     * @return CaptionTrait
     */
    public function setContactAddress($contactAddress)
    {
        $this->contactAddress = $contactAddress;
        return $this;
    }

    /**
     * Get contactAddress.
     *
     * @return string
     */
    public function getContactAddress()
    {
        return $this->contactAddress;
    }

    /**
     * Set contactCity.
     *
     * @param string $contactCity
     *
     * @return CaptionTrait
     */
    public function setContactCity($contactCity)
    {
        $this->contactCity = $contactCity;
        return $this;
    }

    /**
     * Get contactCity.
     *
     * @return string
     */
    public function getContactCity()
    {
        return $this->contactCity;
    }

    /**
     * Set contactCountry.
     *
     * @param string $contactCountry
     *
     * @return CaptionTrait
     */
    public function setContactCountry($contactCountry)
    {
        $this->contactCountry = $contactCountry;
        return $this;
    }

    /**
     * Get contactCountry.
     *
     * @return string
     */
    public function getContactCountry()
    {
        return $this->contactCountry;
    }

    /**
     * Set contactEmail.
     *
     * @param string $contactEmail
     *
     * @return CaptionTrait
     */
    public function setContactEmail($contactEmail)
    {
        $this->contactEmail = $contactEmail;
        return $this;
    }

    /**
     * Get contactEmail.
     *
     * @return string
     */
    public function getContactEmail()
    {
        return $this->contactEmail;
    }

    /**
     * Set contactPhone.
     *
     * @param string $contactPhone
     *
     * @return CaptionTrait
     */
    public function setContactPhone($contactPhone)
    {
        $this->contactPhone = $contactPhone;
        return $this;
    }

    /**
     * Get contactPhone.
     *
     * @return string
     */
    public function getContactPhone()
    {
        return $this->contactPhone;
    }

    /**
     * Set contactState.
     *
     * @param string $contactState
     *
     * @return CaptionTrait
     */
    public function setContactState($contactState)
    {
        $this->contactState = $contactState;
        return $this;
    }

    /**
     * Get contactState.
     *
     * @return string
     */
    public function getContactState()
    {
        return $this->contactState;
    }

    /**
     * Set contactUrl.
     *
     * @param string $contactUrl
     *
     * @return CaptionTrait
     */
    public function setContactUrl($contactUrl)
    {
        $this->contactUrl = $contactUrl;
        return $this;
    }

    /**
     * Get contactUrl.
     *
     * @return string
     */
    public function getContactUrl()
    {
        return $this->contactUrl;
    }

    /**
     * Set contactZip.
     *
     * @param string $contactZip
     *
     * @return CaptionTrait
     */
    public function setContactZip($contactZip)
    {
        $this->contactZip = $contactZip;
        return $this;
    }

    /**
     * Get contactZip.
     *
     * @return string
     */
    public function getContactZip()
    {
        return $this->contactZip;
    }

    /**
     * Set copyright.
     *
     * @param string $copyright
     *
     * @return CaptionTrait
     */
    public function setCopyright($copyright)
    {
        $this->copyright = $copyright;
        return $this;
    }

    /**
     * Get copyright.
     *
     * @return string
     */
    public function getCopyright()
    {
        return $this->copyright;
    }

    /**
     * Set copyrightUrl.
     *
     * @param string $copyrightUrl
     *
     * @return CaptionTrait
     */
    public function setCopyrightUrl($copyrightUrl)
    {
        $this->copyrightUrl = $copyrightUrl;
        return $this;
    }

    /**
     * Get copyrightUrl.
     *
     * @return string
     */
    public function getCopyrightUrl()
    {
        return $this->copyrightUrl;
    }

    /**
     * Set country.
     *
     * @param string $country
     *
     * @return CaptionTrait
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * Get country.
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set country code.
     *
     * @param string $countryCode
     *
     * @return CaptionTrait
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    /**
     * Get country code.
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Set credit.
     *
     * @param string $credit
     *
     * @return CaptionTrait
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;
        return $this;
    }

    /**
     * Get credit.
     *
     * @return string
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * Set event.
     *
     * @param string $event
     *
     * @return CaptionTrait
     */
    public function setEvent($event)
    {
        $this->event = $event;
        return $this;
    }

    /**
     * Get event.
     *
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set headline.
     *
     * @param string $headline
     *
     * @return CaptionTrait
     */
    public function setHeadline($headline)
    {
        $this->headline = $headline;
        return $this;
    }

    /**
     * Get headline.
     *
     * @return string
     */
    public function getHeadline()
    {
        return $this->headline;
    }

    /**
     * Set instructions.
     *
     * @param string $instructions
     *
     * @return CaptionTrait
     */
    public function setInstructions($instructions)
    {
        $this->instructions = $instructions;
        return $this;
    }

    /**
     * Get instructions.
     *
     * @return string
     */
    public function getInstructions()
    {
        return $this->instructions;
    }

    /**
     * Set location.
     *
     * @param string $location
     *
     * @return CaptionTrait
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * Get location.
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set objectName.
     *
     * @param string $objectName
     *
     * @return CaptionTrait
     */
    public function setObjectName($objectName)
    {
        $this->objectName = $objectName;
        return $this;
    }

    /**
     * Get objectName.
     *
     * @return string
     */
    public function getObjectName()
    {
        return $this->objectName;
    }

    /**
     * Set photographer name.
     *
     * @param string $photographerName
     *
     * @return CaptionTrait
     */
    public function setPhotographerName($photographerName)
    {
        $this->photographerName = $photographerName;
        return $this;
    }

    /**
     * Get photographer name.
     *
     * @return string
     */
    public function getPhotographerName()
    {
        return $this->photographerName;
    }

    /**
     * Set photographerTitle.
     *
     * @param string $photographerTitle
     *
     * @return CaptionTrait
     */
    public function setPhotographerTitle($photographerTitle)
    {
        $this->photographerTitle = $photographerTitle;
        return $this;
    }

    /**
     * Get photographer title in organisation.
     *
     * @return string
     */
    public function getPhotographerTitle()
    {
        return $this->photographerTitle;
    }

    /**
     * Set rights usage terms.
     *
     * @param string $rightsUsageTerms
     *
     * @return CaptionTrait
     */
    public function setRightsUsageTerms($rightsUsageTerms)
    {
        $this->rightsUsageTerms = $rightsUsageTerms;
        return $this;
    }

    /**
     * Get rights usage terms.
     *
     * @return string
     */
    public function getRightsUsageTerms()
    {
        return $this->rightsUsageTerms;
    }

    /**
     * Set source.
     *
     * @param string $source
     *
     * @return CaptionTrait
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * Get source.
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set state/region.
     *
     * @param string $state
     *
     * @return CaptionTrait
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * Get state/region.
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set the IPTC subject codes for the photo.
     *
     * @param string $subjectCodes
     *
     * @return CaptionTrait
     */
    public function setIptcSubjectCodes($subjectCodes)
    {
        $this->iptcSubjectCodes = $subjectCodes;
        return $this;
    }

    /**
     * Get the IPTC subject codes for the photo.
     *
     * @return string
     */
    public function getIptcSubjectCodes()
    {
        return $this->iptcSubjectCodes;
    }

    /**
     * Get iptcScene.
     *
     * @return string
     */
    public function getIptcScene()
    {
        return $this->iptcScene;
    }

    /**
     * Set iptcScene.
     *
     * @param string $iptcScene
     *
     * @return CaptionTrait
     */
    public function setIptcScene($iptcScene)
    {
        $this->iptcScene = $iptcScene;
        return $this;
    }

    /**
     * Set supplemental categories.
     *
     * @param string $supplementalCategories
     *
     * @return CaptionTrait
     */
    public function setSupplementalCategories($supplementalCategories)
    {
        $this->supplementalCategories = $supplementalCategories;
        return $this;
    }

    /**
     * Get supplemental categories.
     *
     * @return string
     */
    public function getSupplementalCategories()
    {
        return $this->supplementalCategories;
    }

    /**
     * Get featuredOrganisationName.
     *
     * @return string
     */
    public function getFeaturedOrganisationName()
    {
        return $this->featuredOrganisationName;
    }

    /**
     * Set featuredOrganisationName.
     *
     * @param string $featuredOrganisationName
     *
     * @return CaptionTrait
     */
    public function setFeaturedOrganisationName($featuredOrganisationName)
    {
        $this->featuredOrganisationName = $featuredOrganisationName;
        return $this;
    }

    /**
     * Get featuredOrganisationCode.
     *
     * @return string
     */
    public function getFeaturedOrganisationCode()
    {
        return $this->featuredOrganisationCode;
    }

    /**
     * Set featuredOrganisationCode.
     *
     * @param string $featuredOrganisationCode
     *
     * @return CaptionTrait
     */
    public function setFeaturedOrganisationCode($featuredOrganisationCode)
    {
        $this->featuredOrganisationCode = $featuredOrganisationCode;
        return $this;
    }

    /**
     * Get intellectualGenre.
     *
     * @return string
     */
    public function getIntellectualGenre()
    {
        return $this->intellectualGenre;
    }

    /**
     * Set intellectualGenre.
     *
     * @param string $intellectualGenre
     *
     * @return CaptionTrait
     */
    public function setIntellectualGenre($intellectualGenre)
    {
        $this->intellectualGenre = $intellectualGenre;
        return $this;
    }

    /**
     * Set urgency.
     *
     * @param integer $urgency
     *
     * @return CaptionTrait
     */
    public function setUrgency($urgency)
    {
        $this->urgency = $urgency;
        return $this;
    }

    /**
     * Get urgency.
     *
     * @return integer
     */
    public function getUrgency()
    {
        return $this->urgency;
    }

    /**
     * Set rating.
     *
     * @param integer $rating
     *
     * @return CaptionTrait
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
        return $this;
    }

    /**
     * Get rating.
     *
     * @return integer
     */
    public function getRating()
    {
        return $this->rating;
    }
}
