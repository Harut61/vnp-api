<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Traits as Traits;
use App\Enums\FoldersTypeEnum;
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
 * @ORM\Table(name="folders")
 * @UniqueEntity(fields={"title"}, message="The title '{{ value }}' is already taken",groups={"write", "folders:write"})
 * @ApiResource(
 *     attributes={
 *         "pagination_client_enabled"=true,
 *         "normalization_context"={"groups"={ "relationship:read",  "folders:read", "read"}, "skip_null_values" = false, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"folders:write", "write"},  "enable_max_depth"=true},
 *         "order"={"id"="DESC"}
 * })
 * @ApiFilter(SearchFilter::class, properties={
 *     "title"="ipartial",
 *     "isActive"="exact",
 *     "id"="exact",
 *     "folder"="exact"
 * })
 * @ApiFilter(OrderFilter::class, properties={"id": "DESC","title": "ASC"}, arguments={"orderParameterName"="order"})
* @ApiFilter(SoftDeletedAtFilter::class, properties={ "deletedAt": "true" })
 * @ApiFilter(PropertyFilter::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @DeletedAtAware(deletedAtFieldName="deleted_at")
 * @ORM\Entity()
 */
class Folders
{
    use Traits\IdTrait;
    use Traits\DatesTrait;
    use Traits\IsActiveTrait;

    /**
     * @var string
     * @Groups({"folders:read", "folders:write", "relationship:read"})
     * @ORM\Column(name="path", type="text", nullable=true)
     */
    public $path;

    /**
     * @var FtpServer
     * @ORM\ManyToOne(targetEntity="FtpServer")
     * @Groups({"folders:read", "folders:write", "relationship:read"})
     * @ORM\JoinColumn(name="ftp_server_id", referencedColumnName="id", nullable=true)
     * @ApiSubresource(maxDepth=1)
     */
    public $ftpServer;

    /**
     * @var Show
     * @ORM\ManyToOne(targetEntity="Show")
     * @Groups({"folders:read", "folders:write", "relationship:read"})
     * @ORM\JoinColumn(name="show_id", referencedColumnName="id", nullable=true)
     * @ApiSubresource(maxDepth=1)
     */
    public $show;

    /**
     * @var AdminUser
     * @ORM\ManyToOne(targetEntity="AdminUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     * @Groups({"folders:read", "folders:write"})
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
     * @Groups({"folders:read", "folders:write", "relationship:read"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $publicationDate;

    /**
     * @var TimeZone
     * @Groups({"folders:write", "folders:read", "relationship:read"})
     * @ORM\ManyToOne(targetEntity="TimeZone")
     * @ORM\JoinColumn(name="time_zone_id", referencedColumnName="id", nullable=true)
     * @ApiSubresource(maxDepth=1)
     */
    public $timeZone;

    /**
     * @var \DateTime Deletion time
     * @ORM\Column( type="datetime", nullable=true)
     * @Groups({"show_retrieval:write", "show_retrieval:read", "relationship:read"})
     */
    public  $dataRetrievalAt;

    /**
     *
     * @ORM\Column( type="string", nullable=true)
     * @Groups({"folders:write", "folders:read", "relationship:read"})
     */
    public  $dataRetrievalStatus;

    /**
     *
     * @ORM\Column( type="string", nullable=true)
     * @Groups({"folders:write", "folders:read", "relationship:read"})
     */
    public  $folder;

    /**
     *
     * @ORM\Column( type="string", nullable=true)
     * @Groups({"folders:write", "folders:read", "relationship:read"})
     */
    public  $subFolder;

    /**
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={
     *             "type"="string",
     *             "enum"={FoldersTypeEnum::FTP,
     *                      FoldersTypeEnum::SFTP,
     *                      FoldersTypeEnum::S3,
     *
     *              },
     *             "example"=FoldersTypeEnum::FTP
     *         }
     *     }
     * )
     * @Assert\Choice(callback={"App\Enums\FoldersTypeEnum", "getConstants"})
     * @Groups({"folders:write", "folders:read", "relationship:read"})
     * @ORM\Column(type="string", options={"default"=FoldersTypeEnum::FTP })
     */
    public $folderType = FoldersTypeEnum::FTP;


    /**
     * @return string
     */
    public function getPublicationDate()
    {
        return ($this->publicationDate) ? $this->publicationDate->format('h:i a') : $this->publicationDate;
    }


    /**
     * @return string
     */
    public function getDataRetrievalAt()
    {
        return $this->formatDateTime($this->dataRetrievalAt);
    }

}
