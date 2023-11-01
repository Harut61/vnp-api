<?php

namespace App\Entity;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\MediaObjectController;
use App\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Annotation\DeletedAtAware;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;


/**
 * @ORM\Entity

 * @Gedmo\Loggable
 * @ApiResource(iri="http://schema.org/MediaObject",
 *     collectionOperations={
 *         "get"={"defaults"={"_cache_by_user"=false}},
 *         "post"={
 *             "method"="POST",
 *             "path"="/media_objects",
 *             "controller"=MediaObjectController::class,
 *             "defaults"={"_api_receive"=false},
 *         },
 *     },
 *     itemOperations={
 *         "get"={"defaults"={"_cache_by_user"=false}},
 *         "put",
 *         "delete",
 *     },
 *     attributes={
 *         "normalization_context"={"groups"={"mediaObject:read", "id:read"}},
 *         "denormalization_context"={"groups"={"media:write"}},
 *     },
 * )
 *
 * @ApiFilter(SearchFilter::class, properties={
 *     "contentUrl"="exact"
 * })
 * @ApiFilter(PropertyFilter::class)
 * @DeletedAtAware(deletedAtFieldName="deleted_at")
 * @Gedmo\Loggable
 * @Vich\Uploadable
 * @ApiFilter(SearchFilter::class, properties={"id"="exact"})
 * @DeletedAtAware(deletedAtFieldName="deleted_at")
 */
class MediaObject
{

    use Traits\IdTrait;
    use Traits\DatesTrait;
    /**
     * @var File|null
     * @Assert\NotNull
     * @Vich\UploadableField(mapping="media_object", fileNameProperty="contentUrl")
     * @Groups({"media:write"})
     */
    public $file;

    /**
     * @var string|null
     * @ORM\Column(nullable=true)
     * @Groups({"mediaObject:read", "mediaObject:write", "source:read"})
     * @ApiProperty(iri="http://schema.org/contentUrl")
     */
    public $contentUrl;


    public function getFile()
    {
        return $this->file;
    }

    public function getContentUrl()
    {
        return getenv("API_ENTRYPOINT") . $this->contentUrl;
    }
}