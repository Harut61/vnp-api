<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Entity\Traits as Traits;
use App\Enums\StoryQAStatusEnum;
use App\Enums\StoryStatusEnum;
use App\Enums\StoryTaggingStatusEnum;
use App\Repository\AdminUserRepository; // A custom constraint
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
 * @ORM\Table(name="high_level_subject_tags")
 * @UniqueEntity(fields={"titleForEndUser"}, message="The titleForEndUser '{{ value }}' is already taken",groups={"write", "high_level_subject_tag:write"})
 * @ApiResource(
 *     attributes={
 *         "pagination_client_enabled"=true,
 *         "normalization_context"={"groups"={ "relationship:read",  "high_level_subject_tag:read", "read"}, "skip_null_values" = false, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"high_level_subject_tag:write", "write"},  "enable_max_depth"=true},
 *         "order"={"position"="DESC"}
 * })
 * @ApiFilter(SearchFilter::class, properties={
 *     "titleForEndUser"="ipartial",
 *     "titleForMarker"="ipartial",
 *     "isActive"="exact",
 *     "id"="exact",
 * })
 * @ApiFilter(OrderFilter::class, properties={
 *     "id": "DESC",
 *     "position": "ASC",
 *     "titleForEndUser": "ASC",
 *     "titleForMarker": "ASC"
 *  },
 * arguments={"orderParameterName"="order"})
* @ApiFilter(SoftDeletedAtFilter::class, properties={ "deletedAt": "true" })
 * @ApiFilter(PropertyFilter::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @DeletedAtAware(deletedAtFieldName="deleted_at")
 * @ORM\Entity()
 */
class HighLevelSubjectTag
{
    use Traits\IdTrait;
    use Traits\DatesTrait;
    use Traits\Position;
    use Traits\IsActiveTrait;
    use Traits\Slug;

    /**
     * @var string
     * @Assert\NotNull
     * @Groups({"relationship:read","general:write", "read", "write", "general:read"})
     * @ORM\Column(type="string", length=255)
     */
    public $vneId;

    /**
     * @var string
     * @Assert\NotNull
     * @Groups({"relationship:read","general:write", "read", "write", "general:read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $vneTitle;

    /**
     * @var AdminUser
     * @ORM\ManyToOne(targetEntity="AdminUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     * @Groups({"high_level_subject_tag:read", "high_level_subject_tag:write"})
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
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Groups({"relationship:read","general:write", "read", "write", "general:read"})
     * @ORM\Column( type="string", length=255, nullable=true)
     */
    public $titleForMarker;

       /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Groups({"relationship:read","general:write", "read", "write", "general:read"})
     * @ORM\Column( type="string", length=255, nullable=true)
     */
    public $titleForEndUser;

    public function setTitleForEndUser($title){
        $this->titleForEndUser = $title;
        $this->setSlug($title);
    }
}
