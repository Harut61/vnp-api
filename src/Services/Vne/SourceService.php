<?php

namespace App\Services\Vne;


use App\Entity\Source;
use App\Entity\Story;
use App\Entity\StoryType;
use App\Exception\GrpcException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Endpoints\Events\AddSourceReply;
use Endpoints\Events\AddSourceRequest;
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

class SourceService extends BaseService
{

    /** @var  EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var  ObjectRepository $repositoy */
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(Source::class);

    }

    /**
     * @param $currentUrl
     * @param $page
     * @param $itemPerPage
     * @param $filter
     * @return array
     */
    public function index($currentUrl , $page, $itemPerPage, $filter):array
    {

        $sourceRequest = new GetSourcesRequest();
        $this->requestType = "GetSources";
        $req = $this->client->GetSources($sourceRequest);

        $response = $req->wait();

        return $this->handleAllResponse($response, $currentUrl, $page, $itemPerPage , $filter);
    }


    public function get($id)
    {

    }

    /**
     * @param $id
     * @return Source|bool
     */
    public function post($id)
    {
        $this->requestType = "AddSource";
        /** @var Source $source */
        $source = $this->repository->find($id);

        if(empty($source)){
            throw new NotFoundHttpException("source not found");
        }

        $newSource = new AddSourceRequest();

        $newSource->setSourceName($source->title);
        $newSource->setVnpId($source->getId());
        $sources = [];
        if(!empty($source->newsMarkets)){
            foreach ($source->newsMarkets as $newsMarket){
                $sources[] = $newsMarket;

            }
        }
        $newSource->setNewsMarketList($sources);
        $newSource->setCreatedBy($source->createdBy->getEmail());
        $res =$this->client->AddSource($newSource);
        $response = $res->wait();

        $this->handleErrorResponse($response);

        /** @var AddSourceReply $response */
        $response = $response[0];

        if($response->getStatus() == "successful"){
            return $source;
        }
        return false;
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
        $list = $response->getSources();
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

    public function handleErrorResponse(array $response): void
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