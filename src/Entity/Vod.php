<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups; // A custom constraint
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use App\Filter\SoftDeletedAtFilter;
use App\Entity\Traits as Traits;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Enums\VodStatusEnum;
use DH\DoctrineAuditBundle\Annotation as Audit;
use App\Annotation\DeletedAtAware;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;

/**
 * Vod
 * @Audit\Auditable
 * @ORM\Table(name="vods")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity()
 *  @ApiResource(
 *         itemOperations={
 *         "get"={"defaults"={"_cache_by_user"=false}},
 *         "put",
 *         "patch","delete",
 *         "post"={
 *             "method"="POST",
 *             "route_name"="vod_upload",
 *             "swagger_context"={
 *
 *                 "parameters"={},
 *                 "responses"={
 *                     "200"={
 *                         "description"="get pre Signed url to upload video",
 *                         "schema"={
 *                             "type"="object",
 *                             "required"={
 *                                 "accessToken"
 *                             },
 *                         }
 *                     },
 *                     "400"={
 *                         "description"="Invalid input"
 *                     },
 *                     "403"={
 *                         "description"="Invalid authentication parameters, or no user was identified."
 *                     }
 *                 },
 *             },
 *         }
 *     },
 *     attributes={
 *         "pagination_client_enabled"=true,
 *         "normalization_context"={"groups"={ "relationship:read",  "vod:read" ,"read",  "id:read"}, "enable_max_depth"=true, "skip_null_values" = false},
 *         "denormalization_context"={"groups"={"vod:write", "write"},  "enable_max_depth"=true},
 *         "order"={"id"="desc"}
 *     })
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @ApiFilter(SoftDeletedAtFilter::class, properties={
 *     "deletedAt": "true"
 * })
 * @ApiFilter(PropertyFilter::class)
 * @DeletedAtAware(deletedAtFieldName="deleted_at")
 */
class Vod
{
    use Traits\IdTrait;
    use Traits\TitleTrait;
    use Traits\DescriptionTrait;
    use Traits\DatesTrait;
    use Traits\OnlineTrait;
    use Traits\VodStatusTrait;

    /**
     * @var string
     * @Assert\NotNull
     * @Groups({"relationship:read", "vod:read", "vod:write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $originalExtension;

    /**
     * @var string
     * @Assert\NotNull
     * @Groups({"relationship:read", "vod:read", "vod:write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $originalFileName;

    /**
     * @var string
     * @Groups({"relationship:read", "vod:read", "vod:write"})
     * @ORM\Column(type="text", nullable=true)
     */
    public $originalFileUrl;

    /**
     * @var string
     * @Groups({"relationship:read", "vod:read", "vod:write"})
     * @ORM\Column(type="text", nullable=true)
     */
    public $originalFileMp4Url;

    /**
     * @var string
     * @Groups({"relationship:read", "vod:read", "vod:write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $originalFileBucket;

    /**
     * @var string
     * @Groups({"relationship:read", "vod:read", "vod:write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $originalFilePath;


    /**
     * @var int
     * @Groups({"relationship:read", "vod:read", "vod:write"})
     * @Assert\Type(type="string")
     * @ORM\Column(name="playBackId", type="string", nullable=true)
     */
    public $playBackId;

    /**
     * @var int
     *  @Groups({"relationship:read", "vod:read", "vod:write"})
     * @ORM\Column(type="integer", length=255, options={"default":0.00})
     */
    public $totalSize = 0.00;


    /**
     * @var int
     *  @Groups({"relationship:read", "vod:read", "vod:write"})
     * @Assert\Type(type="integer")
     * @ORM\Column(type="integer", length=255,nullable=true)
     */
    public $videoWidth;
    /**
     * @var int
     *  @Groups({"relationship:read", "vod:read", "vod:write"})
     * @Assert\Type(type="string")
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    public $videoWidthTxt;

    /**
     * @var int
     *  @Groups({"relationship:read", "vod:read", "vod:write"})
     * @Assert\Type(type="integer")
     * @ORM\Column(type="integer", length=255,nullable=true)
     */
    public $videoHeight;

    /**
     * @var int
     *  @Groups({"relationship:read", "vod:read", "vod:write"})
     * @Assert\Type(type="string")
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    public $videoHeightTxt;

    /**
     * @var int
     *  @Groups({"relationship:read", "vod:read", "vod:write"})
     * @Assert\Type(type="string")
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    public $mediaType;


    /**
     * @var string
     *  @Groups({"relationship:read", "vod:read", "vod:write"})
     * @Assert\Type(type="string")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $duration;

    /**
     * @var int
     *  @Groups({"relationship:read", "vod:read", "vod:write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Type(type="string")
     */
    public $videoCodec;

    /**
     *  @Groups({"relationship:read", "vod:read", "vod:write"})
     * @ORM\Column(type="json", nullable=true)
     */
    public $mediaInfo;

    /**
     * @var int
     *  @Groups({"relationship:read", "vod:read", "vod:write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Type(type="string")
     */
    public $videoFps;

    /**
     * @var int
     *  @Groups({"relationship:read", "vod:read", "vod:write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Type(type="string")
     */
    public $videoFpsTxt;

    /**
     * @var int
     *  @Groups({"relationship:read", "vod:read", "vod:write"})
     * @Assert\Type(type="string")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $videoBitrate;

    /**
     * @var int
     *  @Groups({"relationship:read", "vod:read", "vod:write"})
     * @Assert\Type(type="string")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $videoBitrateTxt;

    /**
     * @var int
     *  @Groups({"relationship:read", "vod:read", "vod:write"})
     * @Assert\Type(type="string")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $displayAspectRation;

    /**
     * @var int
     *  @Groups({"relationship:read", "vod:read", "vod:write"})
     * @Assert\Type(type="string")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $audioCodec;

    /**
     * @var int
     *  @Groups({"relationship:read", "vod:read", "vod:write"})
     * @Assert\Type(type="string")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $audioLanguage;

    /**
     * @var int
     *  @Groups({"relationship:read", "vod:read", "vod:write"})
     * @Assert\Type(type="string")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $audioBitrate;

    /**
     * @var int
     *  @Groups({"relationship:read", "vod:read", "vod:write"})
     * @Assert\Type(type="string")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $audioBitrateTxt;

    /**
     * @var int
     * @Groups({ "relationship:read", "vod:read", "vod:write"})
     * @Assert\Type(type="string")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $videoPath;

    /**
     * @var array
     * @Groups({"vod:read", "vod:write"})
     * @ORM\Column(type="array", nullable=true)
     */
    public $resolutions = [];

    /**
     * @var SourceVideo
     * Once Vod has One SourceVideo
     * @ORM\OneToOne(targetEntity="SourceVideo")
     * @Groups({"vod:write", "vod:read"})
     * @ORM\JoinColumn(name="source_video_id", referencedColumnName="id", nullable=true)
     * @ApiSubresource(maxDepth=1)
     */
    public $sourceVideo;

    /**
     * @var Story
     * Once Vod has One Story
     * @ORM\OneToOne(targetEntity="Story")
     * @Groups({"vod:write", "vod:read"})
     * @ORM\JoinColumn(name="story_id", referencedColumnName="id", nullable=true)
     * @ApiSubresource(maxDepth=1)
     */
    public $story;

    /**
     * @var Interstitial
     * Once Vod has One Interstitial
     * @ORM\OneToOne(targetEntity="Interstitial")
     * @Groups({"vod:write", "vod:read"})
     * @ORM\JoinColumn(name="interstitial_id", referencedColumnName="id", nullable=true)
     * @ApiSubresource(maxDepth=1)
     */
    public $interstitial;


    /**
     * @return int
     */
    public function getVideoPath()
    {
        return getenv("VOD_ENDPOINT").$this->videoPath;
    }
}
