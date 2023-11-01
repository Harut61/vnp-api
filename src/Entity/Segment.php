<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Entity\Traits as Traits;
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
 * @ORM\Table(name="segment")
 * @UniqueEntity(fields={"vneId"}, message="The vneId '{{ value }}' is already taken")
 * @ApiResource(
 *     attributes={
 *         "pagination_client_enabled"=true,
 *         "normalization_context"={"groups"={ "relationship:read",  "segment:read", "read"}, "skip_null_values" = false, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"segment:write", "write"},  "enable_max_depth"=true},
 *
 * })
 * @ApiFilter(SearchFilter::class, properties={
 *     "title"="ipartial",
 *     "isActive"="exact",
 *     "id"="exact",
 * })
 * @ApiFilter(PropertyFilter::class)

* @ApiFilter(SoftDeletedAtFilter::class, properties={ "deletedAt": "true" })
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @DeletedAtAware(deletedAtFieldName="deleted_at")
 * @ORM\Entity()
 */
class Segment
{
    use Traits\IdTrait;
    use Traits\TitleTrait;
    use Traits\DatesTrait;
    use Traits\IsActiveTrait;

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Groups({"relationship:read","segment:write", "segment:read"})
     * @ORM\Column(type="string", length=255)
     */
    public $vneId;

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Groups({"relationship:read","segment:write", "segment:read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $vneTitle;

    /**
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={
     *             "type"="string",
     *             "enum"={SegmentTypeEnum::INTRO,
     *                      SegmentTypeEnum::SEGMENT_INTRO,
     *                      SegmentTypeEnum::OUTRO,
     *                      SegmentTypeEnum::STORY_TRANSITION,
     *                      SegmentTypeEnum::TROUBLE_LOOP,
     *                      SegmentTypeEnum::SUBSCRIBE_NOW
     *              },
     *             "example"=SegmentTypeEnum::INTRO
     *         }
     *     }
     * )
     * @Assert\Choice(callback={"App\Enums\SegmentTypeEnum", "getConstants"})
     * @Groups({"segment:write", "segment:read", "relationship:read"})
     * @ORM\Column(type="string", length=20, options={"default"=SegmentTypeEnum::INTRO })
     */
    public $type = SegmentTypeEnum::INTRO;
}
