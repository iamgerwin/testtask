<?php
/**
 * User: Gerwin
 * Date: 04/02/2019
 * Time: 1:33 AM
 */
declare(strict_types=1);

namespace App\Database\Entities\MailChimp;

use Doctrine\ORM\Mapping as ORM;
use EoneoPay\Utils\Str;

/**
 * @ORM\Entity()
 */
class MailChimpMember extends MailChimpEntity
{
    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    private $memberId;

    /**
     * @ORM\Column(name="list_id", type="string")
     *
     * @var string
     */
    private $listId;

    /**
     * @ORM\Column(name="email_address", type="string")
     *
     * @var string
     */
    private $emailAddress;

    /**
     * @ORM\Column(name="email_type", type="string", nullable=true)
     *
     * @var string
     */
    private $emailType;

    /**
     * @ORM\Column(name="location", type="array", nullable=true)
     *
     * @var array
     */
    private $location;

    /**
     * @ORM\Column(name="vip", type="boolean", nullable=true)
     *
     * @var boolean
     */
    private $vip;

    /**
     * @ORM\Column(name="language", type="string")
     *
     * @var string
     */
    private $language;

    /**
     * @ORM\Column(name="interests", type="array", nullable=true)
     *
     * @var array
     */
    private $interests;

    /**
     * @ORM\Column(name="status", type="string")
     *
     * @var string
     */
    private $status;

    /**
     * @ORM\Column(name="merge_fields", type="array", nullable=true)
     *
     * @var array
     */
    private $mergeFields;

    /**
     * @ORM\Column(name="ip_signup", type="string", nullable=true)
     *
     * @var string
     */
    private $ipSignup;

    /**
     * @ORM\Column(name="timestamp_signup", type="string", nullable=true)
     *
     * @var string
     */
    private $timestampSignup;

    /**
     * @ORM\Column(name="tags", type="array", nullable=true)
     *
     * @var array
     */
    private $tags;


    /**
     * @return string
     */
    public function getId() : string
    {
        return $this->memberId;
    }

    /**
     * @param string $memberId
     * @return MailChimpMember
     */
    public function setId(string $memberId) : MailChimpMember
    {
        $this->memberId = $memberId;
        return $this;
    }

    /**
     * @return string
     */
    public function getListId() : string
    {
        return $this->listId;
    }

    /**
     * @param string $listId
     * @return MailChimpMember
     */
    public function setListId(string $listId) : MailChimpMember
    {
        $this->listId = $listId;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmailAddress() : string
    {
        return $this->emailAddress;
    }

    /**
     * @param string $emailAddress
     * @return MailChimpMember
     */
    public function setEmailAddress(string $emailAddress) : MailChimpMember
    {
        $this->emailAddress = $emailAddress;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmailType() : string
    {
        return $this->emailType;
    }

    /**
     * @param string $emailType
     * @return MailChimpMember
     */
    public function setEmailType(string $emailType) : MailChimpMember
    {
        $this->emailType = $emailType;
        return $this;
    }

    /**
     * @return array
     */
    public function getLocation() : array
    {
        return $this->location;
    }

    /**
     * @param array $location
     * @return MailChimpMember
     */
    public function setLocation(array $location) : MailChimpMember
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVip() : bool
    {
        return $this->vip;
    }

    /**
     * @param bool $vip
     * @return MailChimpMember
     */
    public function setVip(bool $vip) : MailChimpMember
    {
        $this->vip = $vip;
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage() : string
    {
        return $this->language;
    }

    /**
     * @param string $language
     * @return MailChimpMember
     */
    public function setLanguage(string $language) : MailChimpMember
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @return array
     */
    public function getInterests() : array
    {
        return $this->interests;
    }

    /**
     * @param array $interests
     * @return MailChimpMember
     */
    public function setInterests(array $interests) : MailChimpMember
    {
        $this->interests = $interests;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus() : string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return MailChimpMember
     */
    public function setStatus(string $status) : MailChimpMember
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return array
     */
    public function getMergeFields() : array
    {
        return $this->mergeFields;
    }

    /**
     * @param array $mergeFields
     * @return MailChimpMember
     */
    public function setMergeFields(array $mergeFields) : MailChimpMember
    {
        $this->mergeFields = $mergeFields;
        return $this;
    }

    /**
     * @return string
     */
    public function getIpSignup() : string
    {
        return $this->ipSignup;
    }

    /**
     * @param string $ipSignup
     * @return MailChimpMember
     */
    public function setIpSignup(string $ipSignup) : MailChimpMember
    {
        $this->ipSignup = $ipSignup;
        return $this;
    }

    /**
     * @return string
     */
    public function getTimestampSignup() : string
    {
        return $this->timestampSignup;
    }

    /**
     * @param string $timestampSignup
     * @return MailChimpMember
     */
    public function setTimestampSignup(string $timestampSignup) : MailChimpMember
    {
        $this->timestampSignup = $timestampSignup;
        return $this;
    }

    /**
     * @return array
     */
    public function getTags() : array
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     * @return MailChimpMember
     */
    public function setTags(array $tags) : MailChimpMember
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * Get validation rules for mailchimp entity.
     *
     * @return array
     */
    public function getValidationRules() : array
    {
        return [
            'mail_chimp_member_id' => 'nullable|string',
            'list_id' => 'required|string|exists:App\Database\Entities\MailChimp\MailChimpList,mail_chimp_id',
            'email_address' => 'required|string',
            'email_type' => 'nullable|string',
            'location' => 'nullable|array',
            'location.latitude' => 'nullable|numeric',
            'location.longitude' => 'nullable|numeric',
            'vip' => 'required|boolean',
            'language' => 'nullable|string',
            'interests' => 'nullable|array',
            'status' => 'required|string|in:subscribed,unsubscribed,cleaned,pending',
            'merge_fields' => 'nullable|array',
            'ip_signup' => 'nullable|string',
            'timestamp_signup' => 'nullable|date_format:"Y-m-d H:i:s"',
            'tags' => 'nullable|array',
        ];
    }

    /**
     * Get array representation of entity.
     *
     * @return array
     */
    public function toArray() : array
    {
        $array = [];
        $string = new Str();

        foreach (\get_object_vars($this) as $property => $value) {
            $array[$string->snake($property)] = $value;
        }

        return $array;
    }
}