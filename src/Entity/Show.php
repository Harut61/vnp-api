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
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use DH\DoctrineAuditBundle\Annotation as Audit;
use App\Filter\SoftDeletedAtFilter;
use App\Enums\GenderEnum;
use App\Annotation\DeletedAtAware;
use App\Repository\ShowRepository;

/**
 * @Audit\Auditable
 * @ORM\Entity(repositoryClass=ShowRepository::class)
 * @ORM\Table(name="shows")
 * @UniqueEntity(fields={"title"}, message="The title '{{ value }}' is already taken",groups={"write", "show:write"})
 * @ApiResource(
 *     attributes={
 *         "pagination_client_enabled"=true,
 *         "normalization_context"={"groups"={ "relationship:read",  "show:read", "read"}, "skip_null_values" = false, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"show:write", "write"},  "enable_max_depth"=true},
 *         "order"={"position"="DESC"}
 * })
 * @ApiFilter(SearchFilter::class, properties={
 *     "title"="ipartial",
 *     "isActive"="exact",
 *     "id"="exact",
 *     "source.title"="exact",
 *
 * })
 *  @ApiFilter(PropertyFilter::class)
 * @ApiFilter(OrderFilter::class, properties={
 *     "id": "DESC",
 *     "position": "ASC",
 *     "title": "ASC" }, arguments={"orderParameterName"="order"})
* @ApiFilter(SoftDeletedAtFilter::class, properties={ "deletedAt": "true" })
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @DeletedAtAware(deletedAtFieldName="deleted_at")
 */
class Show
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
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     * })
     * @Groups({"show:read", "show:write"})
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
     * @Groups({"show:write", "show:read", "relationship:read"})
     * @ORM\ManyToMany(targetEntity="Source")
     * @ApiSubresource(maxDepth=1)
     */
    public $localDropIns = [];

    /**
     * @Groups({"show:write", "show:read", "relationship:read"})
     * @ORM\ManyToOne(targetEntity="Source")
     * @ORM\JoinColumn(name="source_id", referencedColumnName="id", nullable=true)
     * @ApiSubresource(maxDepth=1)
     */
    public $source;

    /**
     * @Groups({"show:read", "show:write"})
     * @ORM\Column(type="string", nullable=true)
     */
    public $showDuration;

    /**
     * @Groups({"show:read", "show:write"})
     * @ORM\Column(type="json")
     */
    public $delayNewsMarketList = [];

}
