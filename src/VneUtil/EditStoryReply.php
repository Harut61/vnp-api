<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: events.proto

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * The reply containing the message from VNE.
 *Sample Reply body:
 *{
 *'story_id':'f31ce620-ac1e-4a1b-aa14-82a1ec3bd417'
 *}
 *
 * Generated from protobuf message <code>EditStoryReply</code>
 */
class EditStoryReply extends \Google\Protobuf\Internal\Message
{
    /**
     *Unique story ID (UUID)
     *
     * Generated from protobuf field <code>string story_id = 1;</code>
     */
    protected $story_id = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $story_id
     *          Unique story ID (UUID)
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

}

