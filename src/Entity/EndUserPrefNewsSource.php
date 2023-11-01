<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Repository\EndUserPrefNewsSourceRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits as Traits;
use App\Filter\SoftDeletedAtFilter;
use Gedmo\Mapping\Annotation as Gedmo;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *   attributes={
 *       "pagination_client_enabled"=true,
 *       "normalization_context"={"groups"={ "relationship:read",  "end_user_pref_news_sources:read", "read"}, "skip_null_values" = false, "enable_max_depth"=true},
 *       "denormalization_context"={"groups"={"end_user_pref_news_sources:write", "write"},  "enable_max_depth"=true},
 *       "order"={"id"="desc"}
 *   }
 *)
 * @ApiFilter(SearchFilter::class, properties={
 *     "id"="exact",
 * })
 * @ApiFilter(OrderFilter::class, properties={
 *     "id": "DESC",
 * }, arguments={"orderParameterName"="order"})
 * @ApiFilter(SoftDeletedAtFilter::class, properties={ "deletedAt": "true" })
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @ORM\Entity(repositoryClass=EndUserPrefNewsSourceRepository::class)
 */
class EndUserPrefNewsSource
{

    use Traits\IdTrait;
    use Traits\DatesTrait;

    /**
     * @Groups({ "end_user_pref_news_sources:read", "end_user_pref_news_sources:write"})
     * @ORM\ManyToOne(targetEntity=Source::class, inversedBy="endUserPrefNewsSources")
     */
    public $source;

    /**
     * @var EndUser
     * @ORM\ManyToOne(targetEntity="EndUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     * @Groups({ "end_user_pref_news_sources:read", "end_user_pref_news_sources:write"})
     * @ApiSubresource(maxDepth=1)
     * * @ApiProperty(
     *     attributes={
     *         "swagger_context"={
     *             "type"="string",
     *             "example"="/end_users/{id}",
     *         },
     *     },
     * )
     */
    public $createdBy;

    /**
     * @Groups({ "end_user_pref_news_sources:read", "end_user_pref_news_sources:write"})
     * @ORM\Column(type="boolean", nullable=true)
     */
    public $preference_status;

}
