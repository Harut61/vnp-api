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
 *{'status': 'successful'}
 *
 * Generated from protobuf message <code>endpoints.events.AddSourceReply</code>
 */
class AddSourceReply extends \Google\Protobuf\Internal\Message
{
    /**
     *Status of operation
     *
     * Generated from protobuf field <code>string status = 1;</code>
     */
    protected $status = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $status
     *          Status of operation
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Events::initOnce();
        parent::__construct($data);
    }

    /**
     *Status of operation
     *
     * Generated from protobuf field <code>string status = 1;</code>
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     *Status of operation
     *
     * Generated from protobuf field <code>string status = 1;</code>
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
