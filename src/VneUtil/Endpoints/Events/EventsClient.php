<?php
// GENERATED CODE -- DO NOT EDIT!

// Original file comments:
//
// GRPC events in VNE and service definition.
namespace Endpoints\Events;

/**
 */
class EventsClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \Endpoints\Events\PingRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Ping(\Endpoints\Events\PingRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/Ping',
        $argument,
        ['\Endpoints\Events\PingReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\AddUserRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function AddUser(\Endpoints\Events\AddUserRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/AddUser',
        $argument,
        ['\Endpoints\Events\AddUserReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\AddStoryRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function AddStory(\Endpoints\Events\AddStoryRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/AddStory',
        $argument,
        ['\Endpoints\Events\AddStoryReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\CreateLineupRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function CreateLineup(\Endpoints\Events\CreateLineupRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/CreateLineup',
        $argument,
        ['\Endpoints\Events\CreateLineupReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\DeleteStoryRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function DeleteStory(\Endpoints\Events\DeleteStoryRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/DeleteStory',
        $argument,
        ['\Endpoints\Events\DeleteStoryReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\DeleteUserRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function DeleteUser(\Endpoints\Events\DeleteUserRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/DeleteUser',
        $argument,
        ['\Endpoints\Events\DeleteUserReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\EditUserRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function EditUser(\Endpoints\Events\EditUserRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/EditUser',
        $argument,
        ['\Endpoints\Events\EditUserReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\EditStoryRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function EditStory(\Endpoints\Events\EditStoryRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/EditStory',
        $argument,
        ['\Endpoints\Events\EditStoryReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\GetSimilarStoriesRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetSimilarStories(\Endpoints\Events\GetSimilarStoriesRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/GetSimilarStories',
        $argument,
        ['\Endpoints\Events\GetSimilarStoriesReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\GetStoryTagsRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetStoryTags(\Endpoints\Events\GetStoryTagsRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/GetStoryTags',
        $argument,
        ['\Endpoints\Events\GetStoryTagsReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\GetPreferencesRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetPreferences(\Endpoints\Events\GetPreferencesRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/GetPreferences',
        $argument,
        ['\Endpoints\Events\GetPreferencesReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\GetUserStoryRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetUserStory(\Endpoints\Events\GetUserStoryRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/GetUserStory',
        $argument,
        ['\Endpoints\Events\GetUserStoryReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\ReSyncRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function ReSync(\Endpoints\Events\ReSyncRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/ReSync',
        $argument,
        ['\Endpoints\Events\ReSyncReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\AddSourceRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function AddSource(\Endpoints\Events\AddSourceRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/AddSource',
        $argument,
        ['\Endpoints\Events\AddSourceReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\EditSourceRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function EditSource(\Endpoints\Events\EditSourceRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/EditSource',
        $argument,
        ['\Endpoints\Events\EditSourceReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\GetSourcesRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetSources(\Endpoints\Events\GetSourcesRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/GetSources',
        $argument,
        ['\Endpoints\Events\GetSourcesReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\DeleteSourceRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function DeleteSource(\Endpoints\Events\DeleteSourceRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/DeleteSource',
        $argument,
        ['\Endpoints\Events\DeleteSourceReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\GetNewsMarketRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetNewsMarket(\Endpoints\Events\GetNewsMarketRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/GetNewsMarket',
        $argument,
        ['\Endpoints\Events\GetNewsMarketReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\AddPreferenceRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function AddPreference(\Endpoints\Events\AddPreferenceRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/AddPreference',
        $argument,
        ['\Endpoints\Events\AddPreferenceReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\DeletePreferenceRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function DeletePreference(\Endpoints\Events\DeletePreferenceRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/DeletePreference',
        $argument,
        ['\Endpoints\Events\DeletePreferenceReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\GetCountiesRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetCounties(\Endpoints\Events\GetCountiesRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/GetCounties',
        $argument,
        ['\Endpoints\Events\GetCountiesReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\GetStoryTypeRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetStoryType(\Endpoints\Events\GetStoryTypeRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/GetStoryType',
        $argument,
        ['\Endpoints\Events\GetStoryTypeReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\GetHighLevelSubjectRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetHighLevelSubject(\Endpoints\Events\GetHighLevelSubjectRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/GetHighLevelSubject',
        $argument,
        ['\Endpoints\Events\GetHighLevelSubjectReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\AddSourceNewsMarketRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function AddSourceNewsMarket(\Endpoints\Events\AddSourceNewsMarketRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/AddSourceNewsMarket',
        $argument,
        ['\Endpoints\Events\AddSourceNewsMarketReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\DeleteSourceNewsMarketRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function DeleteSourceNewsMarket(\Endpoints\Events\DeleteSourceNewsMarketRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/DeleteSourceNewsMarket',
        $argument,
        ['\Endpoints\Events\DeleteSourceNewsMarketReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\AddShowRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function AddShow(\Endpoints\Events\AddShowRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/AddShow',
        $argument,
        ['\Endpoints\Events\AddShowReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\EditShowRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function EditShow(\Endpoints\Events\EditShowRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/EditShow',
        $argument,
        ['\Endpoints\Events\EditShowReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\DeleteShowRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function DeleteShow(\Endpoints\Events\DeleteShowRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/DeleteShow',
        $argument,
        ['\Endpoints\Events\DeleteShowReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\GetNodeRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetNode(\Endpoints\Events\GetNodeRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/GetNode',
        $argument,
        ['\Endpoints\Events\GetNodeReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\GetSegmentsListRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetSegmentsList(\Endpoints\Events\GetSegmentsListRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/GetSegmentsList',
        $argument,
        ['\Endpoints\Events\GetSegmentsListReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Endpoints\Events\GetEntitiesRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetEntities(\Endpoints\Events\GetEntitiesRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/endpoints.events.Events/GetEntities',
        $argument,
        ['\Endpoints\Events\GetEntitiesReply', 'decode'],
        $metadata, $options);
    }

}
