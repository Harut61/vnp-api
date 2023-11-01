<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Traits as Traits;
use App\Enums\UserStatusEnum;
use App\Repository\EndUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiSubresource;
use DH\DoctrineAuditBundle\Annotation as Audit;
use App\Filter\SoftDeletedAtFilter;
use App\Enums\GenderEnum;
use Doctrine\ORM\Mapping\UniqueConstraint;
use App\Annotation\DeletedAtAware;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;

/**
 * @Audit\Auditable
 * @ORM\Table(name="end_users",
 *     *    uniqueConstraints={
 *        @UniqueConstraint(name="end_user_social_name",
 *            columns={"social_name"})
 *    })
 * @UniqueEntity(fields={"email"}, message="The username '{{ value }}' is already taken",groups={"registration", "write"})
 * @UniqueEntity(fields={"socialName"}, message="The socialName '{{ value }}' is already taken",groups={"registration", "write"})
 * @ApiResource(
 *     attributes={
 *         "pagination_client_enabled"=true,
 *         "normalization_context"={"groups"={ "relationship:read",  "end_user:read", "read"}, "skip_null_values" = false, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"end_user:write", "write", "registration"},  "enable_max_depth"=true},
 *         "order"={"id"="desc"}
 * })
 * @ApiFilter(SearchFilter::class, properties={
 *
 *     "chosenName"="exact",
 *     "email"="exact"
 *
 *
 * })
 * @ApiFilter(PropertyFilter::class)
* @ApiFilter(SoftDeletedAtFilter::class, properties={ "deletedAt": "true" })
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @DeletedAtAware(deletedAtFieldName="deleted_at")
 * @ORM\Entity(repositoryClass=EndUserRepository::class)
 */
class EndUser implements UserInterface
{
    use Traits\IdTrait;
    use Traits\DatesTrait;

    /**
     *
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\Length(min=5, groups={"registration"})
     * @Groups({"end_user:read", "end_user:write"})
     * @ORM\Column(type="string", length=255)
     */
    public $username;

    /**
     * @Groups({"end_user:read", "end_user:write", "relationship:read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $firstName;

    /**
     * @Assert\NotBlank(groups={"registration"})
     * @Groups({"end_user:read", "end_user:write", "relationship:read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $chosenName;

    /**
     * @Assert\NotBlank(groups={"registration"})
     * @Groups({"end_user:read", "relationship:read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $socialName;

    /**
     * @Groups({"end_user:read", "end_user:write", "relationship:read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $lastName;

    /**
     * @Groups({"end_user:read", "end_user:write", "relationship:read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $mobileNumber;

    /**
     * @Groups({"end_user:read", "end_user:write", "relationship:read"})
     * @ORM\Column(type="integer", length=255 , options={"default"=5 })
     */
    public $preferedLineupDuration = 5;

    /**
     *  @Assert\Email(
     *     message="The email is not a valid email.",
     *     groups={"registration"}
     * )
     * @Assert\NotBlank(groups={"registration"})
     * @Groups({"end_user:read", "end_user:write", "relationship:read"})
     * @ORM\Column(type="string", length=255)
     */
    public $email;

    /**
     * @Audit\Ignore
     * @Groups({"end_user:write"})
     * @ORM\Column(type="text", nullable=true)
     */
    public $password;

    /**
     * @Groups({"end_user:read", "end_user:write"})
     * @ORM\Column(type="boolean")
     */
    public $enabled = false;

    /**
     * @Groups({"end_user:read", "end_user:write"})
     * @ORM\Column(type="boolean")
     */
    public $blocked = false;


    /**
     * @Groups({"end_user:read", "end_user:write"})
     * @ORM\Column(type="string" , nullable=true)
     */
    public $blockReason = "";

    /**
     * @Assert\Choice(callback={"App\Enums\UserStatusEnum", "getConstants"})
     * @Groups({"end_user:write", "read"})
     * @ORM\Column(type="string", length=10, options={"default"=UserStatusEnum::PENDING })
     */
    public $userStatus = UserStatusEnum::PENDING;

    /**
     * @Groups({"end_user:read", "end_user:write"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $birthDate;

    /**
     * @Audit\Ignore
     * @Groups({"end_user:read", "end_user:write"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $lastLogin;

    /**
     * @Groups({"end_user:read", "end_user:write"})
     * @ORM\Column(type="boolean")
     */
    public $mobileNumberVerified = false;

    /**
     * @Groups({"end_user:read", "end_user:write"})
     * @ORM\Column(type="json")
     */
    public $roles = [];


    /**
     * @Assert\Choice(callback={"App\Enums\GenderEnum", "getConstants"})
     * @Groups({"end_user:write", "read"})
     * @ORM\Column(type="string", length=10, options={"default"=GenderEnum::MALE })
     */
    public $gender = GenderEnum::MALE;

    /**
     * @Audit\Ignore
     * @Groups({"end_user:read", "end_user:write"})
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    public $otp;

    /**
     * @Groups({"end_user:read", "end_user:write"})
     * @ORM\Column(type="integer")
     */
    public $numberOfDevices = 1;

    /**
     * @Groups({"end_user:read", "end_user:write"})
     * @ORM\Column(type="boolean")
     */
    public $emailVerified = false;

    /**
     * @Groups({"end_user:read", "end_user:write"})
     * @ORM\Column(type="boolean")
     */
    public $signUpStatus = false;


    /**
     * @Audit\Ignore
     * @Groups({"end_user:read", "end_user:write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $emailVerificationToken;

    /**
     * @var string
     * @Groups({"registration"})
     * @Assert\Length(min=8, groups={"registration"})
     */
    public $plainPassword;

    /**
     * @Audit\Ignore
     * @Groups({"end_user:write"})
     * @ORM\Column(type="json",  nullable=true)
     */
    public $tokens = [];

    /**
     * 
     * @Groups({"end_user:read", "end_user:write"})
     * @ORM\Column(type="text", nullable=true)
     */
    public $profilePicUrl;

    /**
     * @Groups({"end_user:read", "end_user:write"})
     * @ORM\Column(type="string", nullable=true)
     */
    public $facebookId;

    /**
     * @Groups({"end_user:read", "end_user:write"})
     * @ORM\Column(type="string", nullable=true)
     */
    public $googleId;

    /**
     * @Groups({"end_user:read", "end_user:write"})
     * @ORM\Column(type="string", nullable=true)
     */
    public $appleId;

    /**
     * @Groups({"end_user:read", "end_user:write"})
     *  @ORM\Column(type="boolean", nullable=true)
     */
    public $isApplePrivateEmail = 0;

    /**
     * @Groups({"end_user:read", "end_user:write"})
     * @ORM\Column(type="string", nullable=true)
     */
    public $applePrivateEmail;

    /**
     * @Groups({"end_user:read", "end_user:write"})
     * @ORM\Column(type="json")
     */
    public $newsMarketList = [];


    // TODO return list of all dma get it from vne or create new route
    /**
     * @Groups({"end_user:read", "end_user:write"})
     */
    public $dmaList;

    /**
     * @ORM\OneToMany(targetEntity=EndUserPrefNewsSource::class, mappedBy="user_id")
     */
    private $endUserPrefNewsSources;

    public function __construct()
    {
        $this->endUserPrefNewsSources = new ArrayCollection();
    }


    /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param mixed $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }



    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->plainPassword = $password;
    }

    /**
     * @param string $password
     */
    public function updatePassword($password)
    {
        $this->password = $password;
    }

    /**
     * @param string $password
     */
    public function updateChosenName($chosenName)
    {
        $this->chosenName = $chosenName;
        return $this;
    }

    public function getSalt()
    {
        // The bcrypt and argon2i algorithms don't require a separate salt.
        // You *may* need a real salt if you choose a different encoder.
        return null;
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getRoles(): array
    {
        return ["ROLE_USER"];
    }

    public function setSocialName($chosenName)
    {

        $chosenName = preg_replace('~[^\pL\d]+~u', '_', $chosenName);

        $chosenName = preg_replace('~[^-\w]+~', '', $chosenName);

        $chosenName = trim($chosenName, '_');

        $chosenName = preg_replace('~-+~', '_', $chosenName);

        $chosenName = strtolower($chosenName);

        if (empty($chosenName)) {
            return 'n-a';
        }

        $this->socialName = $chosenName.time();
        return $this;
    }

    /**
     * @return Collection|EndUserPrefNewsSource[]
     */
    public function getEndUserPrefNewsSources(): Collection
    {
        return $this->endUserPrefNewsSources;
    }

    public function addEndUserPrefNewsSource(EndUserPrefNewsSource $endUserPrefNewsSource): self
    {
        if (!$this->endUserPrefNewsSources->contains($endUserPrefNewsSource)) {
            $this->endUserPrefNewsSources[] = $endUserPrefNewsSource;
            $endUserPrefNewsSource->setUserId($this);
        }

        return $this;
    }

    public function removeEndUserPrefNewsSource(EndUserPrefNewsSource $endUserPrefNewsSource): self
    {
        if ($this->endUserPrefNewsSources->removeElement($endUserPrefNewsSource)) {
            // set the owning side to null (unless already changed)
            if ($endUserPrefNewsSource->getUserId() === $this) {
                $endUserPrefNewsSource->setUserId(null);
            }
        }

        return $this;
    }

}
