<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Traits as Traits;
use App\Enums\UserStatusEnum;
use App\Repository\AdminUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection; // A custom constraint
// DON'T forget the following use statement!!!
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
use App\Annotation\DeletedAtAware;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;

/**
 * @Audit\Auditable
 * @ORM\Table(name="admin_users")
 * @UniqueEntity(fields={"email"}, message="The username '{{ value }}' is already taken",groups={"registration", "write"})
 * @ApiResource(
 *     attributes={
 *         "pagination_client_enabled"=true,
 *         "normalization_context"={"groups"={ "relationship:read",  "admin:read", "read"}, "skip_null_values" = false, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"admin:write", "write", "registration"},  "enable_max_depth"=true},
 *         "order"={"id"="desc"}
 * })
 * @ApiFilter(SearchFilter::class, properties={
 *     "fullName"="ipartial",
 *     "username"="partial",
 *     "email"="partial",
 *     "adminRoles.title"="partial",
 *     "userStatus"="partial",
 *     "roles"="partial",
 *     "id"="exact"
 *
 * })
 * @ApiFilter(SoftDeletedAtFilter::class, properties={
 *     "deletedAt": "true"
 * })
 *
 * @ApiFilter(PropertyFilter::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @DeletedAtAware(deletedAtFieldName="deleted_at")
 * @ORM\Entity(repositoryClass=AdminUserRepository::class)
 */
class AdminUser implements UserInterface
{
    use Traits\IdTrait;
    use Traits\DatesTrait;

    /**
     * @Groups({"admin:read", "admin:write", "write",  "relationship:read"})
     * @ORM\Column(type="text", nullable=true)
     */
    public $contactInfo;

    /**
     *
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\Length(min=5, groups={"registration"})
     * @Groups({"admin:read", "admin:write"})
     * @ORM\Column(type="string", length=255)
     */
    public $username;

    /**
     * @Groups({"admin:read", "admin:write", "relationship:read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $fullName;


    /**
     *  @Assert\Email(
     *     message="The email is not a valid email.",
     *     groups={"registration"}
     * )
     * @Assert\NotBlank(groups={"registration"})
     * @Groups({"admin:read", "admin:write", "relationship:read"})
     * @ORM\Column(type="string", length=255)
     */
    public $email;

    /**
     * @Audit\Ignore
     * @Groups({"admin:write"})
     * @ORM\Column(type="text", nullable=true)
     */
    public $password;

    /**
     * @Groups({"admin:read", "admin:write"})
     * @ORM\Column(type="boolean")
     */
    public $enabled = false;

    /**
     * @Groups({"admin:read", "admin:write"})
     * @ORM\Column(type="boolean")
     */
    public $blocked = false;


    /**
     * @Groups({"admin:read", "admin:write"})
     * @ORM\Column(type="string" , nullable=true)
     */
    public $blockReason = "";

    /**
     * @Assert\Choice(callback={"App\Enums\UserStatusEnum", "getConstants"})
     * @Groups({"admin:write", "read"})
     * @ORM\Column(type="string", length=10, options={"default"=UserStatusEnum::PENDING })
     */
    public $userStatus = UserStatusEnum::PENDING;

    /**
     * @Groups({"admin:read", "admin:write"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $birthDate;

    /**
     * @Audit\Ignore
     * @Groups({"admin:read", "admin:write"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $lastLogin;

    /**
     * @Groups({"admin:read", "admin:write"})
     * @ORM\Column(type="boolean")
     */
    public $mobileNumberVerified = false;

    /**
     * @Groups({"admin:read", "admin:write"})
     * @ORM\Column(type="json")
     */
    public $roles = [];


    /**
     * @Assert\Choice(callback={"App\Enums\GenderEnum", "getConstants"})
     * @Groups({"admin:write", "read"})
     * @ORM\Column(type="string", length=10, options={"default"=GenderEnum::MALE })
     */
    public $gender = GenderEnum::MALE;

    /**
     * @Audit\Ignore
     * @Groups({"admin:read", "admin:write"})
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    public $otp;

    /**
     * @Groups({"admin:read", "admin:write"})
     * @ORM\Column(type="integer")
     */
    public $numberOfDevices = 1;

    /**
     * @Groups({"admin:read", "admin:write"})
     * @ORM\Column(type="boolean")
     */
    public $emailVerified = false;

    /**
     * @Audit\Ignore
     * @Groups({"admin:read", "admin:write"})
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
     *
     * @ORM\ManyToMany(targetEntity="AdminRoles" , inversedBy="users")
     * @Groups({"admin:read", "admin:write"})
     * @ApiSubresource(maxDepth=1)
     */
    public $adminRoles;

    /**
     * @Audit\Ignore
     * @Groups({"admin:write"})
     * @ORM\Column(type="json",  nullable=true)
     */
    public $tokens = [];

    /**
     * @ORM\OneToMany(targetEntity=SourceVideoQaIssuesType::class, mappedBy="created_by")
     */
    private $sourceVideoQaIssuesTypes;

    /**
     * @ORM\OneToMany(targetEntity=SourceVideoQaIssues::class, mappedBy="created_by")
     */
    private $sourceVideoQaIssues;

    /**
     * @ORM\OneToMany(targetEntity=StoryQaIssuesType::class, mappedBy="created_by")
     */
    private $storyQaIssuesTypes;

    /**
     * @ORM\OneToMany(targetEntity=StoryQaIssue::class, mappedBy="created_by")
     */
    private $storyQaIssues;

    public function __construct()
    {
        $this->sourceVideoQaIssuesTypes = new ArrayCollection();
        $this->sourceVideoQaIssues = new ArrayCollection();
        $this->storyQaIssuesTypes = new ArrayCollection();
        $this->storyQaIssues = new ArrayCollection();
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
        $roles = [];

        if($this->adminRoles){
            /** @var AdminRoles $adminRole */
            foreach ($this->adminRoles as $adminRole) {
                array_push($roles, $adminRole->code);
            }
        }

        return array_unique($roles);
    }

    /**
     * {@inheritdoc}
     */
    public function hasRole($role)
    {
        return \in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * @param AdminRoles $adminRole
     * @return AdminUser
     */
    public function addAdminRoles(AdminRoles $adminRole): self
    {
        $this->adminRoles[] = $adminRole;

        return $this;
    }

    /**
     * @return Collection|SourceVideoQaIssuesType[]
     */
    public function getSourceVideoQaIssuesTypes(): Collection
    {
        return $this->sourceVideoQaIssuesTypes;
    }

    public function addSourceVideoQaIssuesType(SourceVideoQaIssuesType $sourceVideoQaIssuesType): self
    {
        if (!$this->sourceVideoQaIssuesTypes->contains($sourceVideoQaIssuesType)) {
            $this->sourceVideoQaIssuesTypes[] = $sourceVideoQaIssuesType;
            $sourceVideoQaIssuesType->setCreatedBy($this);
        }

        return $this;
    }

    public function removeSourceVideoQaIssuesType(SourceVideoQaIssuesType $sourceVideoQaIssuesType): self
    {
        if ($this->sourceVideoQaIssuesTypes->removeElement($sourceVideoQaIssuesType)) {
            // set the owning side to null (unless already changed)
            if ($sourceVideoQaIssuesType->getCreatedBy() === $this) {
                $sourceVideoQaIssuesType->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|SourceVideoQaIssues[]
     */
    public function getSourceVideoQaIssues(): Collection
    {
        return $this->sourceVideoQaIssues;
    }

    public function addSourceVideoQaIssue(SourceVideoQaIssues $sourceVideoQaIssue): self
    {
        if (!$this->sourceVideoQaIssues->contains($sourceVideoQaIssue)) {
            $this->sourceVideoQaIssues[] = $sourceVideoQaIssue;
            $sourceVideoQaIssue->setCreatedBy($this);
        }

        return $this;
    }

    public function removeSourceVideoQaIssue(SourceVideoQaIssues $sourceVideoQaIssue): self
    {
        if ($this->sourceVideoQaIssues->removeElement($sourceVideoQaIssue)) {
            // set the owning side to null (unless already changed)
            if ($sourceVideoQaIssue->getCreatedBy() === $this) {
                $sourceVideoQaIssue->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|StoryQaIssuesType[]
     */
    public function getStoryQaIssuesTypes(): Collection
    {
        return $this->storyQaIssuesTypes;
    }

    public function addStoryQaIssuesType(StoryQaIssuesType $storyQaIssuesType): self
    {
        if (!$this->storyQaIssuesTypes->contains($storyQaIssuesType)) {
            $this->storyQaIssuesTypes[] = $storyQaIssuesType;
            $storyQaIssuesType->setCreatedBy($this);
        }

        return $this;
    }

    public function removeStoryQaIssuesType(StoryQaIssuesType $storyQaIssuesType): self
    {
        if ($this->storyQaIssuesTypes->removeElement($storyQaIssuesType)) {
            // set the owning side to null (unless already changed)
            if ($storyQaIssuesType->getCreatedBy() === $this) {
                $storyQaIssuesType->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|StoryQaIssue[]
     */
    public function getStoryQaIssues(): Collection
    {
        return $this->storyQaIssues;
    }

    public function addStoryQaIssue(StoryQaIssue $storyQaIssue): self
    {
        if (!$this->storyQaIssues->contains($storyQaIssue)) {
            $this->storyQaIssues[] = $storyQaIssue;
            $storyQaIssue->setCreatedBy($this);
        }

        return $this;
    }

    public function removeStoryQaIssue(StoryQaIssue $storyQaIssue): self
    {
        if ($this->storyQaIssues->removeElement($storyQaIssue)) {
            // set the owning side to null (unless already changed)
            if ($storyQaIssue->getCreatedBy() === $this) {
                $storyQaIssue->setCreatedBy(null);
            }
        }

        return $this;
    }
}
