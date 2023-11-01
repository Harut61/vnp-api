<?php

namespace App\Services\Vne;


use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ObjectRepository;
use Endpoints\Events\EventsClient;
use Endpoints\Events\GetDMAReply;
use Endpoints\Events\GetDMAReplyHelper;
use Endpoints\Events\GetDMARequest;
use Google\Auth\ApplicationDefaultCredentials;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\RepeatedFieldIter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

use Endpoints\Events\PingRequest;
use Grpc\ChannelCredentials;
use Grpc\CallCredentials;
use Grpc\GrpcException;


abstract class BaseService
{
    /**
     * @var EventsClient
     */
    public $client;

    /**
     * @var $requestType string
     */
    public $requestType;

    /**
     * @var ApiClient
     */
    public $apiClient;

    /**
     * @var
     */
    public $apiRoute;

    /**
     * @var SessionInterface
     */
    public $session;

    /** @var $sendMsg */
    public $sendMsg = true;


    /**
     * @var
     */
    protected $createdMsg = "Successfully Created!";


    public function __construct()
    {
        $this->apiClient = new ApiClient();
        $this->getClient();
    }
    /**
     * @return EventsClient
     */
    public function getClient() {
        $channel_credentials = ChannelCredentials::createSsl(null);

        $this->client = new EventsClient(getenv("VNE_HOST"),  [
            'credentials' => $channel_credentials,
            'update_metadata' => function($metaData){
                $metaData[$this->requestType] = ["juzk5pepyz3b98qeksb8reknlwxhfuj8vktsf5awcfbf9rjkt3mewd8hmqnz5z6zrp4b2rq56jptyredermubemnkkd3kj24ckhz6fqrnhrcwqapfuzpppsxlujqwrhv"];
                return $metaData;
            }
        ]);
        return $this->client;
    }



    /**
     * @param $resultSet
     * @param $remaining
     * @param $url
     * @param $page
     * @param $itemPerPage
     * @param $filter
     * @return array
     */
    public function prepareResponse($resultSet, $remaining, $url ,$page, $itemPerPage , $filter)
    {
        $totalItems = count($resultSet) + $remaining;
        $maxPage = ceil($totalItems/$itemPerPage);
        $nextPage = $page+1;

        $collection = [];
        $collection["hydra:member"] =$resultSet;
        $collection["hydra:totalItems"] = count($resultSet) + $remaining;
        $collection["hydra:view"] = [
            "@id" => "$url?itemPerPage=$itemPerPage&page=$page&filter=$filter",
            "@type" => "hydra:PartialCollectionView",
            "hydra:first" => "$url?itemPerPage=$itemPerPage&page=1&filter=$filter"
        ];

        if($nextPage <= $maxPage) {
            $collection["hydra:view"]["hydra:next"] = "$url?itemPerPage=$itemPerPage&page=$nextPage&filter=$filter";
        }

        return $collection;
    }

    /**
     * @param $url
     * @param $param
     * @return mixed|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function postRequest($url, $param)
    {
        $response = $this->apiClient->request("POST", $url, [], $param);
        if($response['status']) {
            return $response["content"];
        } else {
            return $this->errorHandler($response);
        }
    }

    /**
    * @param $url
    * @param $param
    * @return \Symfony\Component\HttpFoundation\RedirectResponse
    */
    public function patchRequest($url, $param){

        $response =  $this->apiClient->request("PATCH",  $url, [], $param);
        if($response['status']){

            return $response["content"];
        }else{
            return $this->errorHandler($response);
        }
    }

    public function errorHandler($response)
    {
        try {
            return $response;
        } catch (\Exception $exception) {
            if ($exception->getCode() == 400) {
                $this->session->invalidate();
            }
        }
    }

    /**
     * @param \DateTime|null $dateTime
     * @return string | null
     */
    public function formatDateTime($dateTime)
    {
        return ($dateTime) ? $dateTime->format('Y-m-d H:i:s') : $dateTime;
    }

}