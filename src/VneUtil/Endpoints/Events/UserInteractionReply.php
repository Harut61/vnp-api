<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: events.proto

namespace Endpoints\Events;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * The reply containing the message from VNE.
 *
 * Generated from protobuf message <code>endpoints.events.UserInteractionReply</code>
 */
class UserInteractionReply extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string reply = 1;</code>
     */
    protected $reply = '';
    /**
     *VNE message for failed response
     *
     * Generated from protobuf field <code>string message = 2;</code>
     */
    protected $message = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $reply
     *     @type string $message
     *          VNE message for failed response
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Events::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string reply = 1;</code>
     * @return string
     */
    public function getReply()
    {
        return $this->reply;
    }

    /**
     * Generated from protobuf field <code>string reply = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setReply($var)
    {
        GPBUtil::checkString($var, True);
        $this->reply = $var;

        return $this;
    }

    /**
     *VNE message for failed response
     *
     * Generated from protobuf field <code>string message = 2;</code>
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     *VNE message for failed response
     *
     * Generated from protobuf field <code>string message = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setMessage($var)
    {
        GPBUtil::checkString($var, True);
        $this->message = $var;

        return $this;
    }

}

