<?php

namespace App\Services\Vne;


use App\Entity\HighLevelSubjectTag;
use App\Entity\Segment;
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
use Endpoints\Events\GetSegmentsListReply;
use Endpoints\Events\GetSegmentsListReplyHelper;
use Endpoints\Events\GetSegmentsListRequest;
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

class SegmentService extends BaseService implements VneServiceInterface
{

    /** @var  EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var  ObjectRepository $repositoy */
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(Segment::class);

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

        $segmentRequest = new GetSegmentsListRequest();
        $this->requestType = "GetSegmentsList";
        $req = $this->client->GetSegmentsList($segmentRequest);

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
        /** @var GetSegmentsListReply $response */
        $response = $response[0];

        /** @var RepeatedField $list */
        $list = $response->getSegmentList();
        /** @var RepeatedFieldIter  $lists */
        $sourceList = $list->getIterator();
        $result = [];
        /** @var  GetSegmentsListReplyHelper $segment */
        foreach ($sourceList as $segment){

            $result[] = [
                "name" => $segment->getName(),
                "id" => $segment->getId()
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