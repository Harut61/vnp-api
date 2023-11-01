<?php

namespace App\Services\Vne;


use App\Entity\Source;
use App\Entity\SourceVideo;
use App\Entity\Story;
use App\Entity\StoryType;
use App\Exception\GrpcException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Endpoints\Events\AddSourceReply;
use Endpoints\Events\AddSourceRequest;
use Endpoints\Events\AddSourceVideoReply;
use Endpoints\Events\AddSourceVideoRequest;
use Endpoints\Events\AddStoryTypeReply;
use Endpoints\Events\AddStoryTypeRequest;
use Endpoints\Events\EditSourceReply;
use Endpoints\Events\EditSourceRequest;
use Endpoints\Events\GetSourcesReply;
use Endpoints\Events\GetSourcesReplyHelper;
use Endpoints\Events\GetSourcesRequest;
use Endpoints\Events\GetStoryTagsRequest;
use Endpoints\Events\GetStoryTypeReply;
use Endpoints\Events\GetStoryTypeReplyHelper;
use Endpoints\Events\GetStoryTypeRequest;
use Endpoints\Events\story_type_helper;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\RepeatedFieldIter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\DateTime;

class SourceVideoService extends BaseService
{

    /** @var  EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var  ObjectRepository $repositoy */
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(SourceVideo::class);

    }


    public function get($id)
    {

    }


    /**
     * @param $id
     * @return SourceVideo|bool
     */
    public function post($id)
    {
        $this->requestType = "AddSourceVideo";
        /** @var SourceVideo $sourceVideo */
        $sourceVideo = $this->repository->find($id);

        if(empty($sourceVideo)){
            throw new NotFoundHttpException("source video not found");
        }

        $newSourceVideo = new AddSourceVideoRequest();

        $newSourceVideo->setShow($sourceVideo->show->title);
        $newSourceVideo->setVnpId($sourceVideo->getId());
        $newSourceVideo->setPublishDateTime($this->formatDateTime($sourceVideo->publicationDate));
        $newSourceVideo->setSource($sourceVideo->show->source->title);
        $newSourceVideo->setCCLink('');
        $newSourceVideo->setVideoLink('');

        $res =$this->client->AddSourceVideo($newSourceVideo);

        $response = $res->wait();

        $this->handleErrorResponse($response);

        /** @var AddSourceVideoReply $response */
        $response = $response[0];

        if($response->getStatus() == "successful"){
            return $sourceVideo;
        }
        return false;
    }

    /**
     * @param \DateTime|null $dateTime
     * @return string | null
     */
    public function formatDateTime($dateTime)
    {
        return ($dateTime) ? $dateTime->format('d M Y H:i') : $dateTime;
    }


    /**
     * @param $id
     * @return Source|bool
     */
    public function put($id)
    {
        $this->requestType = "EditSource";
        /** @var Source $source */
        $source = $this->repository->find($id);
        $editSource = new EditSourceRequest();

        $editSource->setNewSourceName("{$source->getId()}");
        $editSource->setVnpId("{$source->getId()}");

        $res =$this->client->EditSource($editSource);
        $response = $res->wait();

        $this->handleErrorResponse($response);
        /** @var EditSourceReply $response */
        $response = $response[0];

        if($response->getStatus() == "successful"){
            return $source;
        }
        return false;
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    /**
     * @param array $response
     * @param $currentUrl
     * @param $page
     * @param $itemPerPage
     * @param $filter
     * @return array
     */
    public function handleAllResponse(array $response, $currentUrl, $page, $itemPerPage , $filter): array
    {

        /** @var GetSourcesReply $response */
        $response = $response[0];

        /** @var RepeatedField $list */
        $list = $response->
        /** @var RepeatedFieldIter  $lists */
        $sourceList = $list->getIterator();
        $result = [];
        /** @var  GetSourcesReplyHelper $source */
        foreach ($sourceList as $source){

            $result[] = [
                "name" => $source->getSourceName()
            ];
        }
        if($currentUrl){
            return  $this->prepareResponse($result, 0, $currentUrl, $page, $itemPerPage, $filter);
        } else {
           return $result;
        }
    }

    public function handleResponse($response): array
    {

        /** @var GetSourcesRequest $response */
        $response = $response[0];


        /** @var RepeatedField $list */
        $list = $response->getStoryTypeList();
        /** @var RepeatedFieldIter  $lists */
        $sourceList = $list->getIterator();
        $result = [];
        /** @var $storyType */
        foreach ($sourceList as $storyType){
            $result[] = [
                "source" => $storyType
            ];
        }
    }

    public function getRepository(): ObjectRepository
    {
        return $this->repository;
    }

    private function handleErrorResponse(array $response): void
    {
        if ($response[1]->code !== 0) {
            throw new GrpcException(
                sprintf(
                    'gRPC request failed : error code: %s, details: %s',
                    $response[1]->code,
                    $response[1]->details
                )
            );
        }
    }

}