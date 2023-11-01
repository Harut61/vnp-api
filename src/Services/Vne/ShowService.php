<?php

namespace App\Services\Vne;


use App\Entity\Show;
use App\Entity\Source;
use App\Entity\Story;
use App\Entity\StoryType;
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

class ShowService extends BaseService
{

    /** @var  EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var  ObjectRepository $repositoy */
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(Show::class);

    }



    public function get($id)
    {

    }

    /**
     * @param $id
     * @return Show|bool
     */
    public function post($id)
    {
        $this->requestType = "AddShow";
        /** @var Show $show */
        $show = $this->repository->find($id);

        if(empty($show)){
            throw new NotFoundHttpException("Show not found");
        }

        $newShow = new AddShowRequest();

        $newShow->setShowName($show->title);
        $newShow->setCreatedBy($show->createdBy->getEmail());
        $newShow->setVnpId($show->getId());

        $delayList = new DelayNewsMarketListHelper();
        $delayList->setDelay(1);
        $delayList->setNewsMarket("(NON-DMA COUNTIES)");
        $delayListNew = [];
        $delayListNew[] = $delayList;
        $newShow->setDelayNewsMarketList($delayListNew);
        $newShow->setLength($show->showDuration);
        $newShow->setSourceId($show->source->getId());

        $localDropInsList = [];
        foreach ($show->localDropIns as $localDropIn)
        {
            $localDropIns = new LocalDropInsHelper();
            $localDropIns->setTitle($localDropIn->title);
            $localDropIns->setVnpSourceId($localDropIn->getId());
            $localDropInsList[] = $localDropIns;
        }
        $newShow->setLocalDropIns($localDropInsList);
        $res =$this->client->AddShow($newShow);
        $response = $res->wait();
        $this->handleErrorResponse($response);

        /** @var AddShowReply $response */
        $response = $response[0];

        if($response->getStatus() == "successful"){
            return $show;
        }
        return false;
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