<?php

namespace App\Entity;
use App\Repository\SourceRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Entity\Traits as Traits;
use App\Enums\StoryQAStatusEnum;
use App\Enums\StoryStatusEnum;
use App\Enums\StoryTaggingStatusEnum;
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
 * @ORM\Table(name="sources")
 * @ORM\Entity(repositoryClass=SourceRepository::class)
 * @UniqueEntity(fields={"title"}, message="The title '{{ value }}' is already taken",groups={"write", "source:write"})
 * @ApiResource(
 *     attributes={
 *         "pagination_client_enabled"=true,
 *         "normalization_context"={"groups"={ "relationship:read",  "source:read", "read"}, "skip_null_values" = false, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"source:write", "write"},  "enable_max_depth"=true},
 *         "order"={"position"="DESC"}
 * })
 * @ApiFilter(SearchFilter::class, properties={
 *     "title"="ipartial",
 *     "isActive"="exact",
 *     "id"="exact",
 * })
 * @ApiFilter(OrderFilter::class, properties={
 *     "id": "DESC",
 *     "position": "ASC",
 *     "title": "ASC" }, arguments={"orderParameterName"="order"})
 * @ApiFilter(SoftDeletedAtFilter::class, properties={ "deletedAt": "true" })
 * @ApiFilter(PropertyFilter::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @DeletedAtAware(deletedAtFieldName="deleted_at")
 *
 */
class Source
{
    use Traits\IdTrait;
    use Traits\TitleTrait;
    use Traits\DatesTrait;
    use Traits\IsActiveTrait;
    use Traits\Position;
    use Traits\Slug;


    /**
     * @var AdminUser
     * @ORM\ManyToOne(targetEntity="AdminUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     * @Groups({"source:read", "source:write"})
     * @ApiSubresource(maxDepth=1)
     * * @ApiProperty(
     *     attributes={
     *         "swagger_context"={
     *             "type"="string",
     *             "example"="/admin_users/{id}",
     *         },
     *     },
     * )
     */
    public $createdBy;

    /**
     * @Groups({ "source:read", "source:write"})
     * @Assert\NotNull
     * @ORM\Column(type="json", nullable=true)
     */
    public $newsMarkets = [];

    /**
     * @Groups({ "source:read", "source:write"})
     * @ORM\Column(type="string",length=255, nullable=true)
     */
    public $websiteUrl;

    /**
     * @var MediaObject
     * @ORM\ManyToOne(targetEntity="MediaObject")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="media_object", referencedColumnName="id")
     * })
     * @Groups({"source:read", "source:write"})
     * @ApiSubresource(maxDepth=1)
     * * @ApiProperty(
     *     attributes={
     *         "swagger_context"={
     *             "type"="string",
     *             "example"="/media_objects/{id}",
     *         },
     *     },
     * )
     */
    public $mediaObject;

    /**
     * @ORM\OneToMany(targetEntity=EndUserPrefNewsSource::class, mappedBy="source_id")
     */
    private $endUserPrefNewsSources;

    public function __construct()
    {
        $this->endUserPrefNewsSources = new ArrayCollection();
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
            $endUserPrefNewsSource->setSourceId($this);
        }

        return $this;
    }

    public function removeEndUserPrefNewsSource(EndUserPrefNewsSource $endUserPrefNewsSource): self
    {
        if ($this->endUserPrefNewsSources->removeElement($endUserPrefNewsSource)) {
            // set the owning side to null (unless already changed)
            if ($endUserPrefNewsSource->getSourceId() === $this) {
                $endUserPrefNewsSource->setSourceId(null);
            }
        }

        return $this;
    }

}
