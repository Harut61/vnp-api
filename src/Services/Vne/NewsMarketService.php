<?php

namespace App\Services\Vne;


use App\Exception\GrpcException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Endpoints\Events\GetNewsMarketReply;
use Endpoints\Events\GetNewsMarketReplyHelper;
use Endpoints\Events\GetNewsMarketRequest;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\RepeatedFieldIter;
use function PHPSTORM_META\type;

class NewsMarketService extends BaseService
{

    /** @var  EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var  ObjectRepository $repositoy */
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;

    }

    /**
     * @param $currentUrl
     * @param $page
     * @param $itemPerPage
     * @param $filter
     * @return array
     */
    public function index($currentUrl , $page, $itemPerPage, $filter): array
    {

        $skip = ($page == 1) ? 0: $itemPerPage * ($page - 1) ;
        /*var_dump(json_encode(["limit" => $itemPerPage, "skip" => $skip, "filter" => $filter]));
        exit;*/
        $dmaRequest = new GetNewsMarketRequest(["limit" => $itemPerPage, "skip" => $skip, "filter" => $filter]);
        $this->requestType = "GetNewsMarket";
        $req = $this->client->GetNewsMarket($dmaRequest);

        $response = $req->wait();

        return $this->handleAllResponse($response, $currentUrl, $page, $itemPerPage , $filter);
    }


    public function get($id){

    }

    public function post($id)
    {
        return [];
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
        $this->handleErrorResponse($response);
        /** @var GetNewsMarketReply $response */
        $response = $response[0];
        /** @var RepeatedField $list */
        $list = $response->getNewsMarketList();
        /** @var RepeatedFieldIter  $newsMarketList */
        $newsMarketList = $list->getIterator();
        $result = [];
        /** @var GetNewsMarketReplyHelper $newsMarket */
        foreach ($newsMarketList as $newsMarket){
            $result[] = [
                "newsMarket" => $newsMarket->getNewsMarket()
            ];
        }

        return  $this->prepareResponse($result, $response->getRemainingNewsMarket(), $currentUrl, $page, $itemPerPage, $filter);
    }

    public function handleResponse( $response, $currentUrl, $filter): array
    {
        return  [];
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