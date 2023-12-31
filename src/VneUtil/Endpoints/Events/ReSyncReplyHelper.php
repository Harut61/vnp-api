<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: events.proto

namespace Endpoints\Events;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>endpoints.events.ReSyncReplyHelper</code>
 */
class ReSyncReplyHelper extends \Google\Protobuf\Internal\Message
{
    /**
     *Lineup unique ID (UUID)
     *
     * Generated from protobuf field <code>string lineup_id = 1;</code>
     */
    protected $lineup_id = '';
    /**
     *List of story ID
     *
     * Generated from protobuf field <code>repeated string story_ids = 2;</code>
     */
    private $story_ids;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $lineup_id
     *          Lineup unique ID (UUID)
     *     @type string[]|\Google\Protobuf\Internal\RepeatedField $story_ids
     *          List of story ID
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Events::initOnce();
        parent::__construct($data);
    }

    /**
     *Lineup unique ID (UUID)
     *
     * Generated from protobuf field <code>string lineup_id = 1;</code>
     * @return string
     */
    public function getLineupId()
    {
        return $this->lineup_id;
    }

    /**
     *Lineup unique ID (UUID)
     *
     * Generated from protobuf field <code>string lineup_id = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setLineupId($var)
    {
        GPBUtil::checkString($var, True);
        $this->lineup_id = $var;

        return $this;
    }

    /**
     *List of story ID
     *
     * Generated from protobuf field <code>repeated string story_ids = 2;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getStoryIds()
    {
        return $this->story_ids;
    }

    /**
     *List of story ID
     *
     * Generated from protobuf field <code>repeated string story_ids = 2;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setStoryIds($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::STRING);
        $this->story_ids = $arr;

        return $this;
    }

}

