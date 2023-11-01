<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Entity\Traits as Traits;
use App\Enums\InterstitialTimeOfDayEnum;
use App\Enums\SegmentTypeEnum;
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
 * @ORM\Table(name="interstitial")
 * @UniqueEntity(fields={"title"}, message="The title '{{ value }}' is already taken",groups={"write", "source:write"})
 * @ApiResource(
 *     attributes={
 *         "pagination_client_enabled"=true,
 *         "normalization_context"={"groups"={ "relationship:read",  "interstitial:read", "read"}, "skip_null_values" = false, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"interstitial:write", "write"},  "enable_max_depth"=true},
 *
 * })
 * @ApiFilter(SearchFilter::class, properties={
 *     "title"="ipartial",
 *     "id"="exact",
 * })
 * @ApiFilter(SoftDeletedAtFilter::class, properties={ "deletedAt": "true" })
 * @ApiFilter(PropertyFilter::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @DeletedAtAware(deletedAtFieldName="deleted_at")
 * @ORM\Entity()
 */
class Interstitial
{
    use Traits\IdTrait;
    use Traits\TitleTrait;
    use Traits\DatesTrait;
    use Traits\Slug;

    /**
     * @var Segment
     * @ORM\ManyToOne(targetEntity="Segment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="segment", referencedColumnName="id")
     * })
     * @Groups({"interstitial:read", "interstitial:write", "relationship:read"})
     * @ApiSubresource(maxDepth=1)
     * * @ApiProperty(
     *     attributes={
     *         "swagger_context"={
     *             "type"="string",
     *             "example"="/segments/{id}",
     *         },
     *     },
     * )
     */
    public $segment;

    /**
     * @var Vod
     * Once SourceVod has One Vod
     * @ORM\OneToOne(targetEntity="Vod")
     * @Groups({"interstitial:write", "interstitial:read"})
     * @ORM\JoinColumn(name="vod_id", referencedColumnName="id")
     * @ApiSubresource(maxDepth=1)
     */
    public $vod;

    /**
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={
     *             "type"="string",
     *             "enum"={
     *                      InterstitialTimeOfDayEnum::MORNING,
     *                      InterstitialTimeOfDayEnum::AFTERNOON,
     *                      InterstitialTimeOfDayEnum::EVENING
     *
     *
     *              },
     *             "example"=InterstitialTimeOfDayEnum::MORNING
     *         }
     *     }
     * )
     * @Assert\Choice(callback={"App\Enums\InterstitialTimeOfDayEnum", "getConstants"})
     * @Groups({"interstitial:write", "interstitial:read", "relationship:read"})
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    public $timeOfDay;


    /**
     * @Groups({"interstitial:read", "interstitial:write"})
     * @ORM\Column(type="integer", nullable=true)
     */
    public $lastChunkNumber = 0;

    /**
     * @Groups({"interstitial:read", "interstitial:write"} )
     * @ORM\Column(type="float", nullable=true)
     */
    public $lastChunkDuration;

}
