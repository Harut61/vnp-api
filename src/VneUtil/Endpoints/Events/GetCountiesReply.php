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
 * 'County_list': [
 * {'id': 'cc8b7924-e477-4ea9-87a2-c423d7ba572a', 'County_name': 'adams', 'State_name': 'indiana'},
 * {'id': 'c2f8f224-ac04-4e13-89d5-87fef89183fe', 'County_name': 'adams', 'State_name': 'mississippi'},
 * {'id': 'b4320bcd-65cc-420a-90ba-c4af0463846a', 'County_name': 'adams', 'State_name': 'north dakota'},
 * {'id': '21d3cbb5-d405-4e57-8ffc-0368b6eba933', 'County_name': 'adams', 'State_name': 'nebraska'},
 * {'id': '429cdd79-917d-4c3e-b47a-2b844d547906', 'County_name': 'adams', 'State_name': 'ohio'}
 * ],
 * 'remaining_County': 68}
 *
 * Generated from protobuf message <code>endpoints.events.GetCountiesReply</code>
 */
class GetCountiesReply extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>repeated .endpoints.events.GetCountiesReplyHelper County_list = 1;</code>
     */
    private $County_list;
    /**
     * Generated from protobuf field <code>int32 remaining_County = 2;</code>
     */
    protected $remaining_County = 0;
    /**
     * Generated from protobuf field <code>string status = 3;</code>
     */
    protected $status = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \Endpoints\Events\GetCountiesReplyHelper[]|\Google\Protobuf\Internal\RepeatedField $County_list
     *     @type int $remaining_County
     *     @type string $status
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Events::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>repeated .endpoints.events.GetCountiesReplyHelper County_list = 1;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getCountyList()
    {
        return $this->County_list;
    }

    /**
     * Generated from protobuf field <code>repeated .endpoints.events.GetCountiesReplyHelper County_list = 1;</code>
     * @param \Endpoints\Events\GetCountiesReplyHelper[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setCountyList($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Endpoints\Events\GetCountiesReplyHelper::class);
        $this->County_list = $arr;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 remaining_County = 2;</code>
     * @return int
     */
    public function getRemainingCounty()
    {
        return $this->remaining_County;
    }

    /**
     * Generated from protobuf field <code>int32 remaining_County = 2;</code>
     * @param int $var
     * @return $this
     */
    public function setRemainingCounty($var)
    {
        GPBUtil::checkInt32($var);
        $this->remaining_County = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string status = 3;</code>
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Generated from protobuf field <code>string status = 3;</code>
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

