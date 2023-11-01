<?php

namespace App\Services\Vne;

use App\Entity\EndUser;
use App\Entity\LineUp;
use App\Entity\Show;
use App\Exception\GrpcException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Endpoints\Events\AddUserReply;
use Endpoints\Events\AddUserRequest;
use Endpoints\Events\CreateLineupRequest;
use Endpoints\Events\EditShowRequest;
use Endpoints\Events\EditSourceReply;
use Endpoints\Events\GetSourcesReply;
use Endpoints\Events\GetSourcesReplyHelper;
use Endpoints\Events\GetSourcesRequest;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\RepeatedFieldIter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LineupService extends BaseService
{

    /** @var  EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var  ObjectRepository $repositoy */
    private $repository;

    /** @var  Request $request */
    private $request;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(LineUp::class);

    }



    public function get($id)
    {

    }

    /**
     * @param $id
     * @return LineUp|bool
     */
    public function post($id)
    {
        $this->requestType = "CreateLineup";
        /** @var LineUp $lineup */
        $lineup = $this->repository->find($id);

        if(empty($lineup)){
            throw new NotFoundHttpException("Lineup not found");
        }

        $newLineup = new CreateLineupRequest();

        $newLineup->setUserId($lineup->user->getId());
//        $newLineup->setIpAddress($lineup->ipAddress);
//        $newLineup->setPreferredLineupDuration($lineup->lineupDuration);
//        $newLineup->setLatitude($lineup->latitude);
//        $newLineup->setLongitude($lineup->longitude);
        $newLineup->setVnpLineupId($lineup->getId());
        $newLineup->setFirstLineup($lineup->firstLineUp);
//        $newLineup->setRequestTime($this->formatDateTime($lineup->requestedAt));
//        dd($newLineup);
        $res =$this->client->CreateLineup($newLineup);

        $response = $res->wait();
        $this->handleErrorResponse($response);

        /** @var AddUserReply $response */
        $response = $response[0];

        if($response->getStatus() == "successful"){
            return $lineup;
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

    /**
     * @param \DateTime|null $dateTime
     * @return string | null
     */
    public function formatBirthYear($dateTime)
    {
        return ($dateTime) ? $dateTime->format('Y') : $dateTime;
    }



}