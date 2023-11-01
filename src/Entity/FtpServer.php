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
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;

use App\Annotation\DeletedAtAware;

/**
 * @Audit\Auditable
 * @ORM\Table(name="ftp_servers")
 * @UniqueEntity(fields={"title"}, message="The title '{{ value }}' is already taken",groups={"write", "ftpServer:write"})
 * @ApiResource(
 *     attributes={
 *         "pagination_client_enabled"=true,
 *         "normalization_context"={"groups"={ "relationship:read",  "ftpServer:read", "read"}, "skip_null_values" = false, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"ftpServer:write", "write"},  "enable_max_depth"=true},
 *         "order"={"id"="DESC"}
 * })
 * @ApiFilter(PropertyFilter::class)
 * @ApiFilter(SearchFilter::class, properties={
 *     "title"="ipartial",
 *     "isActive"="exact",
 *     "id"="exact",
 * })
 * @ApiFilter(OrderFilter::class, properties={"id": "DESC", "title": "ASC"}, arguments={"orderParameterName"="order"})
*  @ApiFilter(SoftDeletedAtFilter::class, properties={ "deletedAt": "true" })
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @DeletedAtAware(deletedAtFieldName="deleted_at")
 * @ORM\Entity()
 */
class FtpServer
{
    use Traits\IdTrait;
    use Traits\DatesTrait;
    use Traits\IsActiveTrait;

    /**
     *
     * @Assert\NotBlank()
     * @Groups({"ftpServer:write", "ftpServer:read", "relationship:read"})
     * @ORM\Column(type="string", length=255)
     */
    public $username;

    /**
     *
     * @Assert\NotBlank()
     * @Groups({"ftpServer:write", "ftpServer:read", "relationship:read"})
     * @ORM\Column(type="string", length=255)
     */
    public $password;

    /**
     * @Assert\NotBlank()
     * @Groups({"ftpServer:write", "ftpServer:read", "relationship:read"})
     * @ORM\Column(type="string", length=255)
     */
    public $contactName;

    /**
     * @Assert\NotBlank()
     * @Groups({"ftpServer:write", "ftpServer:read", "relationship:read"})
     * @ORM\Column(type="string", length=255)
     */
    public $contactEmail;

    /**
     * @Assert\NotBlank()
     * @Groups({"ftpServer:write", "ftpServer:read", "relationship:read"})
     * @ORM\Column(type="string", length=255)
     */
    public $contactPhone;

    /**
     *
     *@Groups({"ftpServer:write", "ftpServer:read", "relationship:read"})
     * @ORM\Column(type="string", length=255)
     */
    public $port;

    /**
     *
     *@Groups({"ftpServer:write", "ftpServer:read", "relationship:read"})
     * @ORM\Column(type="string", length=255)
     */
    public $host;

    /**
     * @Assert\Choice(callback={"App\Enums\FtpProtocolEnum", "getConstants"})
     * @Groups({"ftpServer:write", "ftpServer:read", "relationship:read"})
     * @ORM\Column(type="string", length=10, options={"default"=FtpProtocolEnum::FTP })
     */
    public $protocol;


}
