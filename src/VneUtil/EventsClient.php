<?php
// GENERATED CODE -- DO NOT EDIT!

// Original file comments:
//
// GRPC events in VNE and service definition.
namespace ;

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
     * @param \PingRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Ping(\PingRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/Events/Ping',
        $argument,
        ['\PingReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \AddUserRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function AddUser(\AddUserRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/Events/AddUser',
        $argument,
        ['\AddUserReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \AddStoryRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function AddStory(\AddStoryRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/Events/AddStory',
        $argument,
        ['\AddStoryReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \CreateLineupRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function CreateLineup(\CreateLineupRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/Events/CreateLineup',
        $argument,
        ['\CreateLineupReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \UserInteractionRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function UserInteraction(\UserInteractionRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/Events/UserInteraction',
        $argument,
        ['\UserInteractionReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \DeleteStoryRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function DeleteStory(\DeleteStoryRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/Events/DeleteStory',
        $argument,
        ['\DeleteStoryReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \DeleteUserRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function DeleteUser(\DeleteUserRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/Events/DeleteUser',
        $argument,
        ['\DeleteUserReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \EditUserRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function EditUser(\EditUserRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/Events/EditUser',
        $argument,
        ['\EditUserReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \EditStoryRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function EditStory(\EditStoryRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/Events/EditStory',
        $argument,
        ['\EditStoryReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GetSimilarStoriesRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetSimilarStories(\GetSimilarStoriesRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/Events/GetSimilarStories',
        $argument,
        ['\GetSimilarStoriesReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GetStoryTagsRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetStoryTags(\GetStoryTagsRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/Events/GetStoryTags',
        $argument,
        ['\GetStoryTagsReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GetTagsRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetTags(\GetTagsRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/Events/GetTags',
        $argument,
        ['\GetTagsReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GetPreferencesRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetPreferences(\GetPreferencesRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/Events/GetPreferences',
        $argument,
        ['\GetPreferencesReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GetUserStoryRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetUserStory(\GetUserStoryRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/Events/GetUserStory',
        $argument,
        ['\GetUserStoryReply', 'decode'],
        $metadata, $options);
    }

}
