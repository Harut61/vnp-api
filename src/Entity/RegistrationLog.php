<?php
namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Entity\Traits as Traits;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use DH\DoctrineAuditBundle\Annotation as Audit;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Filter\SoftDeletedAtFilter;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Annotation\DeletedAtAware;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;

/**
 * @Audit\Auditable
 * @ORM\Table(name="registration_log")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity
 *  @ApiResource(
 *     collectionOperations={
 *         "get", "post",
 *     },
 *     itemOperations={
 *         "get","put","patch","delete"
 *     },
 *      attributes={
 *     "pagination_client_enabled"=true,
 *         "normalization_context"={"groups"={"registrationLog:read", "id:read"}, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"registrationLog:write"},  "enable_max_depth"=true },
 *         "order"={"id"="desc"}
 *     })
 * @ApiFilter(SearchFilter::class, properties={
 *     "email"="exact",
 *     "status"="exact"
 *
 *
 * })
 * @ApiFilter(PropertyFilter::class)
* @ApiFilter(SoftDeletedAtFilter::class, properties={ "deletedAt": "true" })
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @DeletedAtAware(deletedAtFieldName="deleted_at")
 *
 */
class RegistrationLog
{
    use Traits\IdTrait;
    use Traits\DatesTrait;

    /**
     * @var string|null
     * @Groups({"registrationLog:read", "registrationLog:write"})
     * @ORM\Column(name="ip_address", type="string", nullable=true)
     */
    public $ipAddress;

    /**
     * @var string|null
     * @Groups({"registrationLog:read", "registrationLog:write"})
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    public $email;

    /**
     * @var string|null
     * @Groups({"registrationLog:read", "registrationLog:write"})
     * @ORM\Column(name="status", type="string", nullable=true)
     */
    public $status;

    /**
     * @Groups({"registrationLog:read",  "registrationLog:write"})
     * @ORM\Column(type="json",  nullable=true)
     */
    public $postParams = [];

    /**
     * @Groups({"registrationLog:read", "registrationLog:write"})
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    public $message;

    /**
     * @Groups({"registrationLog:read", "registrationLog:write"})
     * @ORM\Column(type="string", nullable=true)
     */
    public $registrationType;

}
