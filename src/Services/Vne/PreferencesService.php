<?php

namespace App\Services\Vne;


use App\Exception\GrpcException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Endpoints\Events\AddPreferenceRequest;
use Endpoints\Events\AddPreferenceReply;
use Endpoints\Events\GetNewsMarketReply;
use Endpoints\Events\GetPreferencesReply;
use Endpoints\Events\GetPreferencesReplyHelper;
use Endpoints\Events\GetNewsMarketRequest;
use Endpoints\Events\GetPreferencesRequest;
use Endpoints\Events\GetVNEDataRequest;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\RepeatedFieldIter;
use function PHPSTORM_META\type;

class PreferencesService extends BaseService
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
    public function index($currentUrl , $page, $itemPerPage, $filter, $userId): array
    {

        $skip = ($page == 1) ? 0: $itemPerPage * ($page - 1) ;
        /*var_dump(json_encode(["limit" => $itemPerPage, "skip" => $skip, "filter" => $filter]));
        exit;*/
        $dmaRequest = new GetPreferencesRequest();
        $dmaRequest->setUserId($userId);
        $this->requestType = "GetPreferences";
        $req = $this->client->GetPreferences($dmaRequest);

        $response = $req->wait();

        return $this->handleAllResponse($response, $currentUrl, $page, $itemPerPage , $filter);
    }


    public function get($id){

    }

    public function setHighLevelPref($userId, $pref, $notPref)
    {
        $this->requestType = "AddPreference";

        $newReq = new AddPreferenceRequest();
        $newReq->setUserId($userId);
        $newReq->setPrefHighlevelSubject($pref);
        $newReq->setNotPrefHighlevelSubject($notPref);
        $res =$this->client->AddPreference($newReq);
        $response = $res->wait();
        $this->handleErrorResponse($response);

        /** @var AddPreferenceReply $response */
        $response = $response[0];

        if($response->getStatus() == "successful"){
            return "High Level Subject Preferences updated successfully";
        }
        return "There was some error while updating High Level Subject Preferences";
    }

    public function setSubjectPref($userId, $pref, $notPref)
    {
        $this->requestType = "AddPreference";

        $newReq = new AddPreferenceRequest();
        $newReq->setUserId($userId);
        $newReq->setPrefSubjects($pref);
        $newReq->setNotPrefSubjects($notPref);
        $res =$this->client->AddPreference($newReq);
        $response = $res->wait();
        $this->handleErrorResponse($response);

        /** @var AddPreferenceReply $response */
        $response = $response[0];

        if($response->getStatus() == "successful"){
            return "Subject Preferences updated successfully";
        }
        return "There was some error while updating Subject Preferences";
    }

    public function setPeopleOrganizationPref($userId, $pref, $notPref)
    {
        $this->requestType = "AddPreference";

        $newReq = new AddPreferenceRequest();
        $newReq->setUserId($userId);
        $newReq->setPrefPeopleOrganization($pref);
        $newReq->setNotPrefPeopleOrganization($notPref);
        $res =$this->client->AddPreference($newReq);
        $response = $res->wait();
        $this->handleErrorResponse($response);

        /** @var AddPreferenceReply $response */
        $response = $response[0];

        if($response->getStatus() == "successful"){
            return "People & Organisation Preferences updated successfully";
        }
        return "There was some error while updating People & Organisation Preferences";
    }

    public function setSourceEntityPref($userId, $pref, $notPref)
    {
        $this->requestType = "AddPreference";

        $newReq = new AddPreferenceRequest();
        $newReq->setUserId($userId);
        $newReq->setPrefSourceEntity($pref);
        $newReq->setNotPrefSourceEntity($notPref);
        $res =$this->client->AddPreference($newReq);
        $response = $res->wait();
        $this->handleErrorResponse($response);

        /** @var AddPreferenceReply $response */
        $response = $response[0];

        if($response->getStatus() == "successful"){
            return "Source Entity Preferences updated successfully";
        }
        return "There was some error while updating Source Entity Preferences";
    }

    public function setLocationPref($userId, $pref, $notPref)
    {
        $this->requestType = "AddPreference";

        $newReq = new AddPreferenceRequest();
        $newReq->setUserId($userId);
        $newReq->setPrefGeo($pref);
        $newReq->setNotPrefGeo($notPref);
        $res =$this->client->AddPreference($newReq);
        $response = $res->wait();
        $this->handleErrorResponse($response);

        /** @var AddPreferenceReply $response */
        $response = $response[0];

        if($response->getStatus() == "successful"){
            return "Location Preferences updated successfully";
        }
        return "There was some error while updating Location Preferences";
    }

    public function setLineupLengthPref($userId, $pref)
    {
        $this->requestType = "AddPreference";

        $newReq = new AddPreferenceRequest();
        $newReq->setUserId($userId);
        $newReq->setPreferredLineupDuration($pref);
        $res =$this->client->AddPreference($newReq);
        $response = $res->wait();
        $this->handleErrorResponse($response);

        /** @var AddPreferenceReply $response */
        $response = $response[0];

        if($response->getStatus() == "successful"){
            return "Lineup Length updated successfully";
        }
        return "There was some error while updating Lineup Length";
    }

    public function setPersonalInterestPref($userId, $pref)
    {
        $this->requestType = "AddPreference";

        $newReq = new AddPreferenceRequest();
        $newReq->setUserId($userId);
        $newReq->setPrefPersonalInterest($pref);
        $res =$this->client->AddPreference($newReq);
        $response = $res->wait();
        $this->handleErrorResponse($response);

        /** @var AddPreferenceReply $response */
        $response = $response[0];

        if($response->getStatus() == "successful"){
            return "Personal Interest updated successfully";
        }
        return "There was some error while updating Personal Interest";
    }

    public function setNewsMarkets($userId, $pref)
    {
        $this->requestType = "AddPreference";

        $newReq = new AddPreferenceRequest();
        $newReq->setUserId($userId);
        $newReq->setNewsMarkets($pref);
        $res =$this->client->AddPreference($newReq);
        $response = $res->wait();
        $this->handleErrorResponse($response);

        /** @var AddPreferenceReply $response */
        $response = $response[0];

        if($response->getStatus() == "successful"){
            return "News Market updated successfully";
        }
        return "There was some error while updating News Market";
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
        /** @var GetPreferencesReply $response */
        $response = $response[0];

        /** @var GetPreferencesReplyHelper $preferences */
        $preferences = $response->getPreferences();

        $result[] = [
            "prefHighlevelSubject" => $preferences->getPrefHighlevelSubject(),
            "notPrefHighlevelSubject" => $preferences->getNotPrefHighlevelSubject(),
            "prefSubject" => $preferences->getPrefSubjects(),
            "notPrefSubject" => $preferences->getNotPrefSubjects(),
            "prefSourceEntity" => $preferences->getPrefSourceEntity(),
            "notPrefSourceEntity" => $preferences->getNotPrefSourceEntity(),
            "prefPeopleOrganization" => $preferences->getPrefPeopleOrganization(),
            "notPrefPeopleOrganization" => $preferences->getNotPrefPeopleOrganization(),
            "prefLocation" => $preferences->getPrefGeo(),
            "notPrefLocation" => $preferences->getNotPrefGeo(),
            "prefLineupDuration" => $preferences->getPrefLineupDuration(),
            "prefPersonalInterest" => $preferences->getPrefPersonalInterest(),
        ];

        return  $this->prepareResponse($result, 0, $currentUrl, $page, $itemPerPage, $filter);
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