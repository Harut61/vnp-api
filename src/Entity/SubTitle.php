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
 * @ORM\Table(name="sub_titles")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity
 * @ApiResource(
 *     iri="http://schema.org/SubTitles",
 *     normalizationContext={
 *         "groups"={"subtitle_object_read"}
 *     },
 *     attributes={
 *         "pagination_client_enabled"=true,
 *         "normalization_context"={"groups"={ "relationship:read",  "read"}, "skip_null_values" = false, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"write"},  "enable_max_depth"=true},
 *         "order"={"id"="desc"}
 *     })
 *   @ApiFilter(SearchFilter::class, properties={
 *     "subLang"="ipartial",
 *     "id"="exact",
 *    })
 *  * @ApiFilter(OrderFilter::class, properties={
 *     "id": "DESC",
 *     "subLang": "ASC"
 *     }, arguments={"orderParameterName"="order"})
 *
* @ApiFilter(SoftDeletedAtFilter::class, properties={ "deletedAt": "true" })
 * @ApiFilter(PropertyFilter::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @DeletedAtAware(deletedAtFieldName="deleted_at")
 *
 */
class SubTitle
{
    use Traits\IdTrait;
    use Traits\DatesTrait;

    /**
     * @var string|null
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Groups({"relationship:read", "read", "write"})
     * @ORM\Column(name="sub_lang", type="string", nullable=true)
     */
    public $subLang;

    
    /**
     * @Groups({"relationship:read", "read"})
     */
    public $contentUrl;
    
   
    /**
     *
     * @Assert\NotNull
     * @Assert\NotBlank
     * @var string|null
     * @Groups({"relationship:read", "read", "write"})
     * @ORM\Column(nullable=true)
     */
    public $resourceUrl;

    /**
    * @return null|string
    */
    public function getContentUrl()
    {
        return getenv("API_ENDPOINT").$this->resourceUrl;
    }
}
