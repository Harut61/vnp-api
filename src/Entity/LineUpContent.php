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
use App\Repository\LineUpContentRepository;

/**
 * @ORM\Table(name="lineup_content")
 * @ApiResource(
 *     attributes={
 *         "pagination_client_enabled"=true,
 *         "normalization_context"={"groups"={ "relationship:read",  "lineUpContent:read", "read"}, "skip_null_values" = false, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"lineUpContent:write", "write"},  "enable_max_depth"=true},
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
 * @ORM\Entity(repositoryClass=LineUpContentRepository::class)
 */
class LineUpContent
{
    use Traits\IdTrait;
    use Traits\DatesTrait;
    use Traits\Position;

    /**
     * @Groups({"lineUpContent:write", "lineUpContent:read", "relationship:read"})
     * @ORM\ManyToOne(targetEntity="LineUp", inversedBy="lineUpContents")
     * @ORM\JoinColumn(name="line_up_id", referencedColumnName="id", nullable=true)
     * @ApiSubresource(maxDepth=1)
     */
    public $lineUp;

    /**
     * @Groups({"lineUpContent:write", "lineUpContent:read", "relationship:read"})
     * @ORM\ManyToOne(targetEntity="Story")
     * @ORM\JoinColumn(name="story_id", referencedColumnName="id", nullable=true)
     * @ApiSubresource(maxDepth=1)
     */
    public $story;

    /**
     * @Groups({"lineUpContent:write", "lineUpContent:read", "relationship:read"})
     * @ORM\ManyToOne(targetEntity="Interstitial")
     * @ORM\JoinColumn(name="interstitial_id", referencedColumnName="id", nullable=true)
     * @ApiSubresource(maxDepth=1)
     */
    public $interstitial;
    
}
