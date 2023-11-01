<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Repository\StoryQaIssuesTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Filter\SoftDeletedAtFilter;
use App\Annotation\DeletedAtAware;
use App\Entity\Traits as Traits;

/**
 * @ApiResource(
 *   attributes={
 *       "pagination_client_enabled"=true,
 *       "normalization_context"={"groups"={ "relationship:read",  "story_qa_issues_types:read", "read"}, "skip_null_values" = false, "enable_max_depth"=true},
 *       "denormalization_context"={"groups"={"story_qa_issues_types:write", "write"},  "enable_max_depth"=true},
 *       "order"={"id"="desc"}
 *})
 *@ApiFilter(SearchFilter::class, properties={
 *     "id"="exact",
 *     "title"="ipartial"
 * })
 * @ApiFilter(OrderFilter::class, properties={
 *     "id": "DESC",
 *     "title": "ASC"
 * }, arguments={"orderParameterName"="order"})
 * @ApiFilter(SoftDeletedAtFilter::class, properties={ "deletedAt": "true" })
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @ORM\Entity(repositoryClass=StoryQaIssuesTypeRepository::class)
 */
class StoryQaIssuesType
{
    use Traits\IdTrait;
    use Traits\TitleTrait;
    use Traits\DatesTrait;

    /**
     * @var AdminUser
     * @ORM\ManyToOne(targetEntity="AdminUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     * @Groups({ "story_qa_issues_types:read", "story_qa_issues_types:write"})
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
     * @Groups({ "story_qa_issues_types:read", "story_qa_issues_types:write"})
     * @ApiSubresource(maxDepth=1)
     * @ORM\OneToMany(targetEntity=StoryQaIssue::class, mappedBy="issue_type")
     */
    public $storyQaIssues;

}
