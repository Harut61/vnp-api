<?php

namespace App\Entity;

use App\Enums\TranscodingQueueStatusEnum;
use App\Repository\TranscodingProfileRepository;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Traits as Traits;
use App\Repository\AdminUserRepository; // A custom constraint
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiSubresource;
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
use App\Repository\TranscodingQueueRepository;
use App\Annotation\DeletedAtAware;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;

/**
 * @ORM\Entity(repositoryClass=TranscodingQueueRepository::class)
 * @ApiResource(
 *     attributes={
 *         "pagination_client_enabled"=true,
 *         "normalization_context"={"groups"={ "relationship:read",  "transcoding_queue:read", "read"}, "skip_null_values" = false, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"transcoding_queue:write", "write"},  "enable_max_depth"=true},
 *         "order"={"id"="desc"}
 * })
 *
 * @ApiFilter(SearchFilter::class, properties={
 *     "awsJobId"="ipartial",
 *     "vod.id"="exact"
 * })
 * @ApiFilter(PropertyFilter::class)
 * @DeletedAtAware(deletedAtFieldName="deleted_at")
 *
 */
class TranscodingQueue
{
    use Traits\IdTrait;
    use Traits\DatesTrait;

    /**
     * @var string
     * @Assert\NotNull
     * @Groups({"relationship:read", "transcoding_queue:read", "transcoding_queue:write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $awsJobId;

    
    /**
     * @var Vod
     * Once SourceVod has One Vod
     * @ORM\ManyToOne(targetEntity="Vod")
     * @Groups({"sourceVideo:write", "sourceVideo:read"})
     * @ORM\JoinColumn(name="vod_id", referencedColumnName="id")
     * @ApiSubresource(maxDepth=1)
     */
    public $vod;

    /**
     *  @Groups({"relationship:read", "vod:read", "vod:write"})
     * @ORM\Column(type="json", nullable=true)
     */
    public $jobDetails;


    /**
     * @var string
     * @Groups({"relationship:read", "read", "write"})
     * @ORM\Column( type="string", nullable=false)
     * @Assert\Choice(callback={"App\Enums\TranscodingQueueStatusEnum", "getConstants"})
     * @ORM\Column(type="string", length=10, options={"default"=TranscodingQueueStatusEnum::INITIALIZED })
     */
    public $status = TranscodingQueueStatusEnum::INITIALIZED;

    /**
     * @var string
     * @Groups({"relationship:read", "read", "write"})
     * @ORM\Column( type="string", nullable=false)
     */
    public $jobPercentComplete = "";

}
