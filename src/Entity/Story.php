<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Entity\Traits as Traits;
use App\Enums\StoryQAStatusEnum;
use App\Enums\StoryStatusEnum;
use App\Enums\StoryTaggingStatusEnum;
use App\Repository\AdminUserRepository; // A custom constraint
// DON'T forget the following use statement!!!
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
use App\Annotation\DeletedAtAware;
use App\Enums\GenderEnum;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Repository\StoryRepository;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;


/**
 *
 * @Audit\Auditable
 * @ORM\Entity(repositoryClass=StoryRepository::class)
 * @ORM\Table(name="stories")
 * @UniqueEntity(fields={"title"}, message="The title '{{ value }}' is already taken",groups={"write", "story:write"})
 * @ApiResource(
 *     attributes={
 *         "pagination_client_enabled"=true,
 *         "normalization_context"={"groups"={ "relationship:read",  "story:read", "read"}, "skip_null_values" = false, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"story:write", "write"},  "enable_max_depth"=true},
 *         "order"={"id"="desc"}
 * })
 * @ApiFilter(SearchFilter::class, properties={
 *     "title"="ipartial",
 *     "description"="partial",
 *     "email"="partial",
 *     "adminRoles.title"="partial",
 *     "userStatus"="partial",
 *     "roles"="partial",
 *     "id"="exact",
 *     "sourceVideo.title": "exact",
 *     "createdBy.fullName": "exact",
 *     "storyType.title": "exact",
 *     "storyRank": "exact",
 *     "storyStatus": "exact",
 *     "storyQaStatus": "exact"
 *
 * })
 * @ApiFilter(DateFilter::class,properties={"publishedAt"})
 * @ApiFilter(OrderFilter::class, properties={
 *     "storyStartFrame",
 *     "storyEndFrame",
 *     "ledeEndFrame",
 *     "storyRank",
 *     "storyType.title",
 *     "sourceVideo.title",
 *     "publishedAt",
 *     "id",
 *     "storyDuration",
 *     "storyStatus",
 *     "ledeDurationMinutes",
 *     "timeToCreate",
 *     "storyDurationMinutes",
 *     "readyToViewAt",
 *     "ledeDuration",
 *     "storyQaStatus"
 * }, arguments={"orderParameterName"="order"})
 *
 * @ApiFilter(SoftDeletedAtFilter::class, properties={ "deletedAt": "true" })
 * @ApiFilter(PropertyFilter::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @DeletedAtAware(deletedAtFieldName="deleted_at")
 *
 */
class Story
{
    use Traits\IdTrait;
    use Traits\TitleTrait;
    use Traits\DatesTrait;


    /**
     * @Groups({"story:read", "story:write"} )
     * @ORM\Column(type="text", nullable=true)
     */
    public $description;


    /**
     * @Groups({"story:read", "story:write"})
     * @ORM\Column(type="json", nullable=true)
     */
    public $storyMeta = [];

    /**
     * @var AdminUser
     * @ORM\ManyToOne(targetEntity="AdminUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     * @Groups({"story:read", "story:write"})
     * @ApiSubresource(maxDepth=1)
     * * @ApiProperty(
     *     attributes={
     *         "swagger_context"={
     *             "type"="string",
     *             "example"="/users/{id}",
     *         },
     *     },
     * )
     */
    public $createdBy;


    /**
     * @Groups({"story:read", "story:write"})
     * @ORM\Column(type="integer")
     */
    public $thumbnailFrame;

    /**
     * @Groups({"story:read", "story:write", "relationship:read"})
     * @ORM\Column(type="integer")
     */
    public $storyStartFrame;

    /**
     * @Groups({"story:read", "story:write", "relationship:read"})
     * @ORM\Column(type="integer")
     */
    public $storyEndFrame;


    /**
     * @Groups({"story:read", "story:write", "relationship:read"})
     * @ORM\Column(type="integer")
     */
    public $ledeEndFrame;


    /**
     * @Groups({"story:read", "story:write", "relationship:read"})
     * @ORM\Column(type="string", nullable=true)
     */
    public $storyStart;

    /**
     * @Groups({"story:read", "story:write", "relationship:read"})
     */
    private $storyStartMinutes;

    /**
     * @Groups({"story:read", "story:write", "relationship:read"})
     */
    private $ledeEndMinutes;


    /**
     * @Groups({"story:read", "story:write", "relationship:read"})
     */
    private $storyEndMinutes;


    /**
     * @Groups({"story:read", "story:write", "relationship:read"})
     * @ORM\Column(type="string", nullable=true)
     */
    public $ledeDuration;


    /**
     * @Groups({"story:read", "story:write","relationship:read"})
     * @ORM\Column(type="string", nullable=true)
     */
    public $storyDuration;

    
    /**
     * @Groups({"story:read", "story:write","relationship:read"})
     * @ORM\Column(type="integer", nullable=true)
     */
    public $lastChunkNumber = 0;


    /**
     * @Groups({"story:read", "story:write", "relationship:read"})
     * @ORM\Column(type="float", nullable=true)
     */
    public $ledeDurationMinutes;


    /**
     * @Groups({"story:read", "story:write","relationship:read"})
     * @ORM\Column(type="float", nullable=true)
     */
    public $storyDurationMinutes;


    /**
     * @Groups({"story:read", "story:write", "relationship:read"})
     * @ORM\Column(type="string", nullable=true)
     */
    public $storyEnd;


    /**
     * @Groups({"story:read", "story:write", "relationship:read"})
     * @ORM\Column(type="string", nullable=true)
     */
    public $ledeEnd;

    /**
     * @Groups({"story:read", "story:write"})
     * @ORM\Column(type="integer", nullable=true)
     */
    public $storyRank;

    /**
     * @Groups({"story:read", "story:write"})
     * @ORM\Column(type="integer", nullable=true)
     */
    public $playlistLength;

    /**
     * @Groups({"story:read", "story:write"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $creationStart;

    /**
     * @Groups({"story:read", "story:write"} )
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $creationEnd;

    /**
     * @Groups({"story:read", "story:write"} )
     * @ORM\Column(type="float", nullable=true)
     */
    public $timeToCreate;

    /**
     * @Groups({"story:read", "story:write"} )
     * @ORM\Column(type="float", nullable=true)
     */
    public $lastChunkDuration;

    /**
     * @Groups({"story:read", "story:write"})
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $publishedAt;

    /**
     * @Groups({"story:read", "story:write", "relationship:read"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $readyToViewAt;

    /**
     * @var bool|null
     * @Groups({"read", "write"})
     * @ORM\Column(type="boolean", options={"default"=false} , nullable=true)
     */
    public $scheduled = false;

    /**
     * @Groups({"story:read", "story:write"} )
     * @ORM\Column(type="text" , nullable=true)
     */
    public $ledeSubTitleText;

    /**
     * @Groups({"story:read", "story:write"} )
     * @ORM\Column(type="text", nullable=true)
     */
    public $restStorySubTitleText;

    /**
     * @Groups({"story:read", "story:write"} )
     * @ORM\Column(type="text", nullable=true)
     */
    public $storyText;

    /**
     * @Assert\Choice(callback={"App\Enums\StoryStatusEnum", "getConstants"})
     * @Groups({"story:write", "story:read"})
     * @ORM\Column(type="string", length=50, options={"default"=StoryStatusEnum::QUEUED })
     */
    public $storyStatus = StoryStatusEnum::QUEUED;

    /**
     * @Assert\Choice(callback={"App\Enums\StoryTaggingStatusEnum", "getConstants"})
     * @Groups({"story:write", "story:read"})
     * @ORM\Column(type="string", length=10, options={"default"=StoryTaggingStatusEnum::QUEUED })
     */
    public $storyTaggingStatus = StoryTaggingStatusEnum::QUEUED;

    /**
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={
     *             "type"="string",
     *             "enum"={
     *                      StoryQAStatusEnum::PENDING_QA,
     *                      StoryQAStatusEnum::QA_ISSUE_FLAGGED,
     *                      StoryQAStatusEnum::QA_REVIEW_PASSED,
     *                      StoryQAStatusEnum::ON_QA_REVIEW,
     *                      StoryQAStatusEnum::TAGGING_COMPLETED
     *              },
     *             "example"=StoryQAStatusEnum::PENDING_QA
     *         }
     *     }
     * )
     * @Assert\Choice(callback={"App\Enums\StoryQAStatusEnum", "getConstants"})
     * @Groups({"story:write", "story:read"})
     * @ORM\Column(type="string", length=10, options={"default"=StoryQAStatusEnum::PENDING_QA })
     */
    public $storyQaStatus = StoryQAStatusEnum::PENDING_QA;

    /**
     *  @ApiProperty(
     *     attributes={
     *         "openapi_context"={
     *             "type"="array",
     *             "example"= ""
     *         }
     *     }
     * )
     * @Groups({"story:write", "story:read","relationship:read"})
     * @ORM\ManyToMany(targetEntity="HighLevelSubjectTag")
     * @ApiSubresource(maxDepth=1)
     */
    public $highLevelSubjectTags = [];

    /**
     * @var SourceVideo
     * @Groups({"story:write", "story:read"})
     * @ORM\ManyToOne(targetEntity="SourceVideo" , inversedBy="stories")
     * @ORM\JoinColumn(name="source_video_id", referencedColumnName="id")
     * @ApiSubresource(maxDepth=1)
     */
    public $sourceVideo;

    /**
     * @var Source
     * @Groups({"story:write", "story:read","relationship:read"})
     * @ORM\ManyToOne(targetEntity="Source")
     * @ORM\JoinColumn(name="source_id", referencedColumnName="id")
     * @ApiSubresource(maxDepth=1)
     */
    public $source;

    /**
     * @var StoryType
     * @Groups({"story:write", "story:read","relationship:read"})
     * @ORM\ManyToOne(targetEntity="StoryType" )
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="story_type_id", referencedColumnName="id", nullable=true)
     * })
     * @ApiSubresource(maxDepth=1)
     */
    public $storyType;

    /**
     * @Groups({"source_video:write", "source_video:read"})
     * @ORM\ManyToMany(targetEntity="SubTitle")
     * @ApiSubresource(maxDepth=1)
     */
    public $subtitles = [];

    /**
     * @Groups({"story:read"})
     */
    private $thumbnailUrl;

    /**
     * @var Vod
     * @Groups({"story:write", "story:read"})
     * Once Story has One Vod
     * @ORM\OneToOne(targetEntity="Vod")
     */
    public $vod;

    /**
     * @var Show
     * @Groups({"story:write", "story:read","relationship:read"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="show_id", referencedColumnName="id", nullable=true)
     * })
     * @ORM\ManyToOne(targetEntity="Show")
     */
    public $show;

    /**
     * @Groups({"story:write", "story:read"})
     * @ORM\Column(name="ccText", type="text", nullable=true)
     */
    public $ccText;

    /**
     * @Groups({"story:write", "story:read"})
     * @ApiSubresource(maxDepth=1)
     * @ORM\OneToMany(targetEntity=StoryQaIssue::class, mappedBy="story")
     */
    public $storyQaIssues;

    /**
     * @var MediaObject
     * @ORM\ManyToOne(targetEntity="MediaObject")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="story_start_image", referencedColumnName="id")
     * })
     * @Groups({"story:read", "story:write"})
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
    public $storyStartImage;

    /**
     * @var MediaObject
     * @ORM\ManyToOne(targetEntity="MediaObject")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="story_end_image", referencedColumnName="id")
     * })
     * @Groups({"story:read", "story:write"})
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
    public $storyEndImage;

    /**
     * @var MediaObject
     * @ORM\ManyToOne(targetEntity="MediaObject")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="story_lede_image", referencedColumnName="id")
     * })
     * @Groups({"story:read", "story:write"})
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
    public $storyLedeImage;

    /**
     * @var MediaObject
     * @ORM\ManyToOne(targetEntity="MediaObject")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="story_thumbnail", referencedColumnName="id")
     * })
     * @Groups({"story:read", "story:write"})
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
    public $storyThumbnail;

    /**
     * @return mixed
     */
    public function getThumbnailUrl()
    {
        $c = $this->thumbnailUrl;
        return getenv('IMG_CDN_ENTRYPOINT').$this->getId()."/thumb.png";
    }

    public function __construct()
    {
        $this->highLevelSubjectTags = new ArrayCollection();
    }


    /**
     * TODO add following fields
     *
     * story_subtitle
     * geo_tags
     * subject_tags
     * organization_tags
     * qa_comments
     *
     */


    /**
     * @return string
     */
    public function getCreationStart()
    {
        return $this->formatDateTime($this->creationStart);
    }


    /**
     * @return string
     */
    public function getCreationEnd()
    {
        return $this->formatDateTime($this->creationEnd);
    }


    /**
     * On Set Ready to View we will calculate time to create
     * @param $readyToView
     * @return $this
     */
    public function setReadyToViewAt($readyToView)
    {
        $this->readyToViewAt = $readyToView;

        if(empty($this->publishedAt) || empty($this->readyToViewAt)) {
            return $this;
        }

        $this->storyDurationMinutes = $this->getStoryEndMinutes() - $this->getStoryStartMinutes();
        $this->ledeDurationMinutes = $this->getLedeEndMinutes() - $this->getStoryStartMinutes();

        /** @var \DateTime $start_date */
        $start_date = $this->publishedAt;
        $this->timeToCreate = $this->secondsToMin($this->intervalToSeconds($start_date->diff($this->readyToViewAt)));
        return $this;
    }


    /**
     * @return string
     */
    public function getPublishedAt()
    {
        return $this->formatDateTime($this->publishedAt);
    }



    /**
     * @return string
     */
    public function getReadyToViewAt()
    {
        return $this->formatDateTime($this->readyToViewAt);
    }



    /**
     * @var
     * @return Show
     */
    public function getShow()
    {
        return $this->sourceVideo->show;
    }


    /**
     * @return float|int
     */
    public function getStoryStartMinutes()
    {
        $seconds = ($this->storyStartFrame / 30);
        return $this->secondsToMin($seconds);
    }


    /**
     * @return float|int
     */
    public function getLedeEndMinutes()
    {
        $seconds = ($this->ledeEndFrame / 30);
        return $this->secondsToMin($seconds);
    }

    /**
     * @return float|int
     */
    public function getStoryEndMinutes()
    {
        $seconds = ($this->storyEndFrame / 30);
        return $this->secondsToMin($seconds);
    }

}
