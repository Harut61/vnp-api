<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\ProfileController;
use App\Entity\Traits as Traits;
use App\Enums\SourceVideoStatusEnum;
use App\Enums\StoryStatusEnum;
use App\Enums\StoryTaggingStatusEnum;
use App\Repository\AdminUserRepository; // A custom constraint
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups; // A custom constraint
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use App\Filter\SoftDeletedAtFilter;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Enums\VodStatusEnum;
use DH\DoctrineAuditBundle\Annotation as Audit;
use App\Annotation\DeletedAtAware;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;

use App\Repository\SourceVideoRepository;

/**
 * Source Video
 * @Audit\Auditable
 * @ORM\Entity(repositoryClass=SourceVideoRepository::class)
 * @ORM\Table(name="source_videos")
 * @ORM\HasLifecycleCallbacks
 *  @ApiResource(
 *         itemOperations={
 *         "get"={"defaults"={"_cache_by_user"=false}},
     *         "put"={"denormalization_context"={"datetime_format"="d-m-Y H:i:s A"}},
 *         "patch","delete"
 *     },
 *      collectionOperations={
 *         "post"={"denormalization_context"={"datetime_format"="d-m-Y H:i:s A"} },
 *         "get"
 *     },
 *     attributes={
 *         "pagination_client_enabled"=true,
 *         "normalization_context"={"groups"={ "relationship:read",  "sourceVideo:read" ,"read",  "id:read"}, "enable_max_depth"=true, "skip_null_values" = false},
 *         "denormalization_context"={"groups"={"sourceVideo:write", "write"}},
 *         "order"={"id"="desc"}
 *     })
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @ApiFilter(SoftDeletedAtFilter::class, properties={
 *     "deletedAt": "true"
 * })
 *  @ApiFilter(SearchFilter::class, properties={
 *     "title": "ipartial",
 *     "status": "exact",
 *     "createdBy.id": "exact",
 *     "uploadedType": "exact",
 *     "createdBy.fullName": "ipartial",
 *     "createdBy.email": "ipartial",
 *     "show.title": "ipartial",
 *     "show.source.title": "ipartial",
 *     "show.localDropIn.title": "ipartial"
 * })
 * @ApiFilter(DateFilter::class,properties={"createdAt", "readyFotMarkupAt"})
 * @ApiFilter(OrderFilter::class, properties={
 *     "source.title",
 *     "show.title",
 *     "storyCount",
 *     "createdAt",
 *     "readyFotMarkupAt",
 *     "storyCount",
 *     "status",
 *     "publicationDate"
 * }, arguments={"orderParameterName"="order"})
 * @ApiFilter(PropertyFilter::class)
 * @DeletedAtAware(deletedAtFieldName="deleted_at")
 */
class SourceVideo
{
    use Traits\IdTrait;
    use Traits\TitleBlankTrait;
    use Traits\DatesTrait;
    use Traits\SourceUploadTypeTrait;

    /**
     *
     * 
     * @Groups({"read", "write"})
     *
     * @ORM\Column(name="slug", type="string",  nullable=true, unique=true)
     */
    private $slug;

    /**
     * @var AdminUser
     * @ApiSubresource(maxDepth=1)
     * @ORM\ManyToOne(targetEntity="AdminUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     * @Groups({"sourceVideo:read", "sourceVideo:write", "relationship:read"})
     */
    public $createdBy;

    /**
     * @var Show
     * @ORM\ManyToOne(targetEntity="Show")
     * @Groups({"sourceVideo:read", "sourceVideo:write", "relationship:read"})
     * @ORM\JoinColumn(name="show_id", referencedColumnName="id", nullable=true)
     * @ApiSubresource(maxDepth=1)
     */
    public $show;


    /**
     * @Groups({"sourceVideo:write", "sourceVideo:read", "relationship:read"})
     * @ORM\ManyToOne(targetEntity="TimeZone")
     * @ORM\JoinColumn(name="time_zone_id", referencedColumnName="id", nullable=true)
     * @ApiSubresource(maxDepth=1)
     */
    public $timeZone;

    /**
     * @Groups({"sourceVideo:read", "relationship:read"})
     */
    private $thumbnailUrl;

    /**
     * @Groups({"sourceVideo:read", "sourceVideo:write", "relationship:read"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $publicationDate;

    /**
     * @Groups({"sourceVideo:read", "sourceVideo:write", "relationship:read"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $markUpStart;

    /**
     * @Groups({"sourceVideo:read", "sourceVideo:write", "relationship:read"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $markUpEnd;

    /**
     * @var \Datetime
     * @Groups({"sourceVideo:read", "sourceVideo:write", "relationship:read"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $readyFotMarkupAt;

    /**
     * @Groups({"sourceVideo:read", "sourceVideo:write", "relationship:read"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $statusUpdatedAt;

    /**
     * @Groups({"sourceVideo:read", "sourceVideo:write", "relationship:read"})
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    public $markUpStatus = false;

    /**
     * @var
     * @Groups({"sourceVideo:read", "sourceVideo:write", "relationship:read"})
     * @ORM\Column(type="json", nullable=true)
     */
    public $beingMarkedUpBy = [];


    /**
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={
     *             "type"="string",
     *             "enum"={SourceVideoStatusEnum::UPLOADED,
     *                      SourceVideoStatusEnum::PROCESSING,
     *                      SourceVideoStatusEnum::READY_FOR_MARKER,
     *                      SourceVideoStatusEnum::PARTIALLY_MARKED_UP,
     *                      SourceVideoStatusEnum::BEING_MARKED_UP,
     *                      SourceVideoStatusEnum::MARKED_UP_FINISHED,
     *                      SourceVideoStatusEnum::ARCHIVED,
     *                      SourceVideoStatusEnum::FAILED
     *              },
     *             "example"=SourceVideoStatusEnum::UPLOADED
     *         }
     *     }
     * )
     * @Assert\Choice(callback={"App\Enums\SourceVideoStatusEnum", "getConstants"})
     * @Groups({"sourceVideo:write", "sourceVideo:read", "relationship:read"})
     * @ORM\Column(type="string", length=20, options={"default"=SourceVideoStatusEnum::UPLOADED })
     */
    public $status = SourceVideoStatusEnum::UPLOADED;

    /**
     * @var Vod
     * Once SourceVod has One Vod
     * @ORM\OneToOne(targetEntity="Vod")
     * @Groups({"sourceVideo:write", "sourceVideo:read"})
     * @ORM\JoinColumn(name="vod_id", referencedColumnName="id")
     * @ApiSubresource(maxDepth=1)
     */
    public $vod;

    /**
     * @Groups({"sourceVideo:write", "sourceVideo:read"})
     * @ORM\OneToMany(targetEntity="Story", mappedBy="sourceVideo")
     * @ApiSubresource(maxDepth=1)
     * @ORM\OrderBy({"storyRank" = "ASC"})
     */
    public $stories = [];

    /**
     * @Groups({"sourceVideo:write", "sourceVideo:read"})
     * @ORM\ManyToMany(targetEntity="SubTitle")
     * @ApiSubresource(maxDepth=1)
     */
    public $subtitles = [];

    /** @var int
     *  @ORM\Column(type="integer")
     * @Groups({"sourceVideo:write", "sourceVideo:read"})
     */
    protected $storyCount  =  0;

    /** @var array
     * @Groups({"sourceVideo:write", "sourceVideo:read"})
     */
    protected $markers  =  [];

    /**
     * @Groups({"sourceVideo:read", "sourceVideo:write", "relationship:read"})
     */
    private $createdAtMinutes;


    /**
     * @Groups({"sourceVideo:read", "sourceVideo:write", "relationship:read"})
     */
    private $readyForMarkupMinutes;

    /**
     * @var MediaObject
     * @ORM\ManyToOne(targetEntity="MediaObject")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="media_object", referencedColumnName="id")
     * })
     * @Groups({"sourceVideo:read", "sourceVideo:write"})
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
     * @Groups({"sourceVideo:read", "sourceVideo:write"})
     * @ApiSubresource(maxDepth=1)
     * @ORM\OneToMany(targetEntity=SourceVideoQaIssues::class, mappedBy="source")
     */
    public $sourceVideoQaIssues;

    /**
     * On Set Ready to Mark we will calculate time to create
     * @return string
     */
    public function getReadyForMarkupMinutes()
    {

        if(empty($this->readyFotMarkupAt)) {
            return '';
        }

        /** @var \DateTime $currentDate */
        $currentDate = new \DateTime();
        return $this->readyForMarkupMinutes =  $this->secondsToMin($this->intervalToSeconds($this->readyFotMarkupAt->diff($currentDate)));
    }

    /**
     * On Set Ready to Mark we will calculate time to create
     *
     * @return string
     */
    public function getCreatedAtMinutes()
    {

        if(empty($this->createdAt)) {
            return '';
        }

        /** @var \DateTime $currentDate */
        $currentDate = new \DateTime();
        return $this->createdAtMinutes =  $this->secondsToMin($this->intervalToSeconds($this->createdAt->diff($currentDate)));
    }


    /**
     * @return mixed
     */
    public function getThumbnailUrl()
    {
        $thumbnail = $this->thumbnailUrl;
        return getenv('IMG_CDN_ENTRYPOINT').$this->getId()."/thumb.png";
    }

    public function __construct()
    {
        $this->localDropIn = new ArrayCollection();
        $this->stories = new ArrayCollection();
        $this->subtitles = new ArrayCollection();
        $this->publicationDate = new \DateTime();
    }

    /**
     * @return string
     */
    public function getPublicationDate()
    {
        return $this->formatDateTime($this->publicationDate);
    }

    /**
     * @return string
     */
    public function getReadyForMarkupAt()
    {
        return $this->formatDateTime($this->readyFotMarkupAt);
    }

    /**
     * @return string
     */
    public function getStatusUpdatedAt()
    {
        return $this->formatDateTime($this->statusUpdatedAt);
    }

    /**
     * @return int
     */
    public function getStoryCount()
    {
        return $this->storyCount;
    }

    /**
     * @return $this
     */
    public function setStoryCount($storyCount)
    {
       $this->storyCount =  $storyCount;
        return $this;
    }

    public function getMarkers()
    {
        $result = [];
        /** @var Story $story */
        foreach ($this->stories as $story) {
            $result[$story->createdBy->getId()] = [
                "fullName" => $story->createdBy->fullName,
                "contactInfo" => $story->createdBy->contactInfo,
                "id" => $story->createdBy->getId()
            ];
        }
        return array_values($result);
    }
    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $this->slugify($slug);
    }


    public function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        //   $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }


    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }
}
