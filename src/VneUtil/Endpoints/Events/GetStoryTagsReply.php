<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: events.proto

namespace Endpoints\Events;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * The reply containing the message from VNE.
 *Sample Reply body:
 *{
 * 'story_id': '4661a552-a0e8-43c9-a916-23940faddc96',
 * 'marker_tag': 'JCL',
 * 'vne_tag': 'JCL'}
 *
 * Generated from protobuf message <code>endpoints.events.GetStoryTagsReply</code>
 */
class GetStoryTagsReply extends \Google\Protobuf\Internal\Message
{
    /**
     *Unique story ID (UUID)
     *
     * Generated from protobuf field <code>string story_id = 1;</code>
     */
    protected $story_id = '';
    /**
     *Marker Tag
     *
     * Generated from protobuf field <code>repeated string marker_tag = 2;</code>
     */
    private $marker_tag;
    /**
     *VNE Tag
     *
     * Generated from protobuf field <code>repeated string vne_tag = 3;</code>
     */
    private $vne_tag;
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
     *     @type string $story_id
     *          Unique story ID (UUID)
     *     @type string[]|\Google\Protobuf\Internal\RepeatedField $marker_tag
     *          Marker Tag
     *     @type string[]|\Google\Protobuf\Internal\RepeatedField $vne_tag
     *          VNE Tag
     *     @type string $status
     *          Status of the progress
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Events::initOnce();
        parent::__construct($data);
    }

    /**
     *Unique story ID (UUID)
     *
     * Generated from protobuf field <code>string story_id = 1;</code>
     * @return string
     */
    public function getStoryId()
    {
        return $this->story_id;
    }

    /**
     *Unique story ID (UUID)
     *
     * Generated from protobuf field <code>string story_id = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setStoryId($var)
    {
        GPBUtil::checkString($var, True);
        $this->story_id = $var;

        return $this;
    }

    /**
     *Marker Tag
     *
     * Generated from protobuf field <code>repeated string marker_tag = 2;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getMarkerTag()
    {
        return $this->marker_tag;
    }

    /**
     *Marker Tag
     *
     * Generated from protobuf field <code>repeated string marker_tag = 2;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setMarkerTag($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::STRING);
        $this->marker_tag = $arr;

        return $this;
    }

    /**
     *VNE Tag
     *
     * Generated from protobuf field <code>repeated string vne_tag = 3;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getVneTag()
    {
        return $this->vne_tag;
    }

    /**
     *VNE Tag
     *
     * Generated from protobuf field <code>repeated string vne_tag = 3;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setVneTag($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::STRING);
        $this->vne_tag = $arr;

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

