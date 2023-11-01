<?php

namespace App\Services\Vne;


use App\Entity\HighLevelSubjectTag;
use App\Entity\Source;
use App\Entity\Story;
use App\Entity\StoryType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Endpoints\Events\AddSourceRequest;
use Endpoints\Events\AddStoryTypeReply;
use Endpoints\Events\AddStoryTypeRequest;
use Endpoints\Events\GetHighLevelSubjectReply;
use Endpoints\Events\GetHighLevelSubjectReplyHelper;
use Endpoints\Events\GetHighLevelSubjectRequest;
use Endpoints\Events\GetSourcesReply;
use Endpoints\Events\GetSourcesReplyHelper;
use Endpoints\Events\GetSourcesRequest;
use Endpoints\Events\GetStoryTagsRequest;
use Endpoints\Events\GetStoryTypeReply;
use Endpoints\Events\GetStoryTypeRequest;
use Endpoints\Events\story_type_helper;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\RepeatedFieldIter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HighLevelSubjectService extends BaseService implements VneServiceInterface
{

    /** @var  EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var  ObjectRepository $repositoy */
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(HighLevelSubjectTag::class);

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

        $highLevelSubjectRequest = new GetHighLevelSubjectRequest();
        $this->requestType = "GetHighLevelSubject";
        $req = $this->client->GetHighLevelSubject($highLevelSubjectRequest);

        $response = $req->wait();

        return $this->handleAllResponse($response, $currentUrl, $page, $itemPerPage , $filter);
    }


    public function get($id)
    {

    }


    public function post($id)
    {

    }


    public function put($id)
    {

    }


    public function delete($id)
    {

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
        /** @var GetHighLevelSubjectReply $response */
        $response = $response[0];

        /** @var RepeatedField $list */
        $list = $response->getHighLevelSubjectList();
        /** @var RepeatedFieldIter  $lists */
        $sourceList = $list->getIterator();
        $result = [];
        /** @var  GetHighLevelSubjectReplyHelper $highLevelSubject */
        foreach ($sourceList as $highLevelSubject){

            $result[] = [
                "name" => $highLevelSubject->getName(),
                "id" => $highLevelSubject->getVneId()
            ];
        }
        if($currentUrl){
            return  $this->prepareResponse($result, 0, $currentUrl, $page, $itemPerPage, $filter);
        } else {
           return $result;
        }
    }

    public function handleResponse($response, $currentUrl, $filter): array
    {


    }

    public function getRepository(): ObjectRepository
    {
        return $this->repository;
    }


}