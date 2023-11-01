<?php

namespace App\Services\Vne;


use App\Entity\Source;
use App\Entity\Story;
use App\Entity\StoryType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Endpoints\Events\AddSourceRequest;
use Endpoints\Events\AddStoryTypeReply;
use Endpoints\Events\AddStoryTypeRequest;
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

class StoryTypeService extends BaseService
{

    /** @var  EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var  ObjectRepository $repositoy */
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(StoryType::class);

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

        $storyTypeRequest = new GetStoryTypeRequest();
        $this->requestType = "GetStoryType";
        $req = $this->client->GetStoryType($storyTypeRequest);

        $response = $req->wait();

        return $this->handleAllResponse($response, $currentUrl, $page, $itemPerPage , $filter);
    }


    public function get($id)
    {

    }

    public function post($id)
    {
        $this->requestType = "AddStoryType";
        /** @var StoryType $storyType */
        $storyType = $this->repository->find($id);

        if(empty($storyType)){
            throw new NotFoundHttpException("story type not found");
        }

        $newSource = new AddStoryTypeRequest();
        $newSource->setName($storyType->title);
        $newSource->setDescription($storyType->title);
        $newSource->setCreatedBy($storyType->createdBy->getEmail());
        $res =$this->client->AddStoryType($newSource);
        $response = $res->wait();
        /** @var AddStoryTypeReply $response */
        return $this->handleResponse($response);
    }

    public function put($id)
    {
        return [];
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
        /** @var GetStoryTypeReply $response */
        $response = $response[0];

        /** @var RepeatedField $list */
        $list = $response->getStoryTypeList();
        /** @var RepeatedFieldIter  $lists */
        $sourceList = $list->getIterator();
        $result = [];
        /** @var  GetStoryTypeReplyHelper $storyType */
        foreach ($sourceList as $storyType){

            $result[] = [
                "name" => $storyType->getName(),
                "id" => $storyType->getVneId(),
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

        /** @var AddStoryTypeReply $response */
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


}