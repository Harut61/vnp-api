<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: events.proto

namespace Endpoints\Events;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>endpoints.events.CreateLineupReply</code>
 */
class CreateLineupReply extends \Google\Protobuf\Internal\Message
{
    /**
     *Unique lineup ID (UUID)
     *
     * Generated from protobuf field <code>string lineup_id = 1;</code>
     */
    protected $lineup_id = '';
    /**
     *list of story id
     *
     * Generated from protobuf field <code>repeated .endpoints.events.SegmentsHelper segments = 2;</code>
     */
    private $segments;
    /**
     *list of segment
     *
     * Generated from protobuf field <code>repeated .endpoints.events.SegmentCountHelper segment_count = 3;</code>
     */
    private $segment_count;
    /**
     *Status of the progress
     *
     * Generated from protobuf field <code>string status = 4;</code>
     */
    protected $status = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $lineup_id
     *          Unique lineup ID (UUID)
     *     @type \Endpoints\Events\SegmentsHelper[]|\Google\Protobuf\Internal\RepeatedField $segments
     *          list of story id
     *     @type \Endpoints\Events\SegmentCountHelper[]|\Google\Protobuf\Internal\RepeatedField $segment_count
     *          list of segment
     *     @type string $status
     *          Status of the progress
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Events::initOnce();
        parent::__construct($data);
    }

    /**
     *Unique lineup ID (UUID)
     *
     * Generated from protobuf field <code>string lineup_id = 1;</code>
     * @return string
     */
    public function getLineupId()
    {
        return $this->lineup_id;
    }

    /**
     *Unique lineup ID (UUID)
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
     *list of story id
     *
     * Generated from protobuf field <code>repeated .endpoints.events.SegmentsHelper segments = 2;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getSegments()
    {
        return $this->segments;
    }

    /**
     *list of story id
     *
     * Generated from protobuf field <code>repeated .endpoints.events.SegmentsHelper segments = 2;</code>
     * @param \Endpoints\Events\SegmentsHelper[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setSegments($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Endpoints\Events\SegmentsHelper::class);
        $this->segments = $arr;

        return $this;
    }

    /**
     *list of segment
     *
     * Generated from protobuf field <code>repeated .endpoints.events.SegmentCountHelper segment_count = 3;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getSegmentCount()
    {
        return $this->segment_count;
    }

    /**
     *list of segment
     *
     * Generated from protobuf field <code>repeated .endpoints.events.SegmentCountHelper segment_count = 3;</code>
     * @param \Endpoints\Events\SegmentCountHelper[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setSegmentCount($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Endpoints\Events\SegmentCountHelper::class);
        $this->segment_count = $arr;

        return $this;
    }

    /**
     *Status of the progress
     *
     * Generated from protobuf field <code>string status = 4;</code>
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     *Status of the progress
     *
     * Generated from protobuf field <code>string status = 4;</code>
     * @param string $var
     * @return $this
     */
    public function setStatus($var)
    {
        GPBUtil::checkString($var, True);
        $this->status = $var;

        return $this;
    }

}
