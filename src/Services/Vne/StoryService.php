<?php

namespace App\Services\Vne;


use App\Entity\HighLevelSubjectTag;
use App\Entity\Show;
use App\Entity\Source;
use App\Entity\Story;
use App\Entity\StoryType;
use App\Enums\VodStatusEnum;
use App\Exception\GrpcException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Endpoints\Events\AddSeriesReply;
use Endpoints\Events\AddSeriesRequest;
use Endpoints\Events\AddShowReply;
use Endpoints\Events\AddShowRequest;
use Endpoints\Events\AddShowRequestDelayNMLHelper;
use Endpoints\Events\AddShowRequestLDIHelper;
use Endpoints\Events\AddSourceReply;
use Endpoints\Events\AddSourceRequest;
use Endpoints\Events\AddStoryReply;
use Endpoints\Events\AddStoryRequest;
use Endpoints\Events\AddStoryTypeReply;
use Endpoints\Events\AddStoryTypeRequest;
use Endpoints\Events\DelayNewsMarketListHelper;
use Endpoints\Events\EditShowRequest;
use Endpoints\Events\EditSourceReply;
use Endpoints\Events\EditSourceRequest;
use Endpoints\Events\GetSourcesReply;
use Endpoints\Events\GetSourcesReplyHelper;
use Endpoints\Events\GetSourcesRequest;
use Endpoints\Events\GetStoryTagsRequest;
use Endpoints\Events\GetStoryTypeReply;
use Endpoints\Events\GetStoryTypeReplyHelper;
use Endpoints\Events\GetStoryTypeRequest;
use Endpoints\Events\LocalDropInsHelper;
use Endpoints\Events\story_type_helper;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\RepeatedFieldIter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StoryService extends BaseService
{

    /** @var  EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var  ObjectRepository $repositoy */
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(Story::class);

    }



    public function get($id)
    {

    }

    /**
     * @param $id
     * @return Story|bool
     */
    public function post($id)
    {
        $this->requestType = "AddStory";
        /** @var Story $story */
        $story = $this->repository->find($id);

        if(empty($story)){
            throw new NotFoundHttpException("Story not found");
        }

        if($story->vod->status == VodStatusEnum::READY) {
            $newStory = new AddStoryRequest();

            $newStory->setStoryId($story->getId());

            $newStory->setVideoUrl($story->vod->originalFileMp4Url);
            $newStory->setTitle($story->title);
            $newStory->setStoryType($story->storyType->vneId);

            $highLevelSubjectTags = [];
            if(!empty($story->highLevelSubjectTags)){
                /** @var HighLevelSubjectTag $highLevelSubjectTag */
                foreach ($story->highLevelSubjectTags as $key => $highLevelSubjectTag){
                    $highLevelSubjectTags[] = $highLevelSubjectTag->vneId;
                }
            }

            $newStory->setStoryHighlevelSubjects($highLevelSubjectTags);
            $newStory->setStoryRank($story->storyRank);
            $newStory->setLedeSubtitleText($story->ledeSubTitleText);
            $newStory->setRestStorySubtitleText($story->restStorySubTitleText);
            $newStory->setStoryStart($story->storyStart);
            $newStory->setLedeEnd($story->ledeEnd);
            $newStory->setStoryEnd($story->storyEnd);
            $newStory->setStoryType($story->storyType->vneId);
            $newStory->setSourceVideoId($story->sourceVideo->getId());


            $res =$this->client->AddStory($newStory);
            $response = $res->wait();
            $this->handleErrorResponse($response);

            /** @var AddStoryReply $response */
            $response = $response[0];

            if($response->getStatus() == "successful"){
                return $story;
            }
            return false;
        }
    }

    /**
     * @param $id
     * @return Show|bool
     */
    public function put($id)
    {
        $this->requestType = "EditShow";
        /** @var Show $show */
        $show = $this->repository->find($id);
        $editShow = new EditShowRequest();

        $editShow->setChangedShowName("{$show->getId()}");
        $editShow->setVnpId("{$show->getId()}");

        $res =$this->client->EditShow($editShow);
        $response = $res->wait();

        $this->handleErrorResponse($response);
        /** @var EditSourceReply $response */
        $response = $response[0];

        if($response->getStatus() == "successful"){
            return $show;
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