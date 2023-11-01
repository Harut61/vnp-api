<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
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
use App\Enums\FtpProtocolEnum;
use App\Annotation\DeletedAtAware;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Repository\LineupRepository;


/**
 * @Audit\Auditable
 * @ORM\Entity(repositoryClass=LineupRepository::class)
 * @ORM\Table(name="lineups")
 * @ApiResource(
 *     attributes={
 *         "pagination_client_enabled"=true,
 *         "normalization_context"={"groups"={ "relationship:read",  "lineUps:read", "read"}, "skip_null_values" = false, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"lineUps:write", "write"},  "enable_max_depth"=true},
 *         "order"={"id"="DESC"}
 * })
 * @ApiFilter(SearchFilter::class, properties={
 *     "title"="ipartial",
 *     "isActive"="exact",
 *     "id"="exact",
 * })
 * @ApiFilter(OrderFilter::class, properties={"id": "DESC", "title": "ASC"}, arguments={"orderParameterName"="order"})
*  @ApiFilter(SoftDeletedAtFilter::class, properties={ "deletedAt": "true" })
 * @ApiFilter(PropertyFilter::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @DeletedAtAware(deletedAtFieldName="deleted_at")
 *
 */
class LineUp
{
    use Traits\IdTrait;
    use Traits\DatesTrait;
    use Traits\IsActiveTrait;

    /**
     * @Groups({"lineUps:write", "lineUps:read", "relationship:read"})
     * @ORM\ManyToOne(targetEntity="EndUser")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     * @ApiSubresource(maxDepth=1)
     */
    public $user;

    /**
     *@Groups({"lineUps:write", "lineUps:read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $vneLineupId;

    /**
     * @Groups({"lineUps:write", "lineUps:read", "relationship:read"})
     * @ORM\ManyToMany(targetEntity=Story::class)
     * @ApiSubresource(maxDepth=1)
     */
    public $stories = [];

    /**
     * @Groups({"lineUps:write", "lineUps:read", "relationship:read"})
     * @ORM\ManyToMany(targetEntity=Interstitial::class)
     * @ApiSubresource(maxDepth=1)
     */
    public $interstitial = [];


    /**
     *@Groups({"lineUps:write", "lineUps:read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $lineupDuration;

    /**
     * @var AdminUser
     * @ORM\ManyToOne(targetEntity="AdminUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     * })
     * @Groups({"lineUps:read", "lineUps:write"})
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
     * @var \DateTime
     * @ORM\Column(name="requested_at", type="datetime", nullable=true)
     * @Groups({"lineUps:read", "lineUps:write"})
     */
    public $requestedAt;

    /**
     * @Groups({"lineUps:read", "lineUps:write"})
     * @ORM\Column(type="boolean")
     */
    public $firstLineUp = false;

    /**
     * @Groups({"lineUps:read", "lineUps:write"})
     * @ORM\Column(type="float", nullable=true)
     */
    public $longitude;

    /**
     * @Groups({"lineUps:read", "lineUps:write"})
     * @ORM\Column(type="float", nullable=true)
     */
    public $latitude;

    /**
     * @var string|null
     * @Groups({"lineUps:read", "lineUps:write"})
     * @ORM\Column(name="ip_address", type="string", nullable=true)
     */
    public $ipAddress;

    /**
     * @Groups({"lineUps:write", "lineUps:read", "relationship:read"})
     * @ORM\OneToMany(targetEntity=LineUpContent::class, mappedBy="lineUp")
     * @ApiSubresource(maxDepth=1)
     */
    public $lineUpContents = [];

    
    /**
     * @Audit\Ignore
     * @Groups({"lineUps:write", "lineUps:read"})
     * @ORM\Column(type="json",  nullable=true)
     */
    public $lineUpContentJson = [];


    
}
