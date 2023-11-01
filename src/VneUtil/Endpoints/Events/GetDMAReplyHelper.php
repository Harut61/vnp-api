<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: events.proto

namespace Endpoints\Events;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>endpoints.events.GetDMAReplyHelper</code>
 */
class GetDMAReplyHelper extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string dma_name = 1;</code>
     */
    protected $dma_name = '';
    /**
     * Generated from protobuf field <code>repeated .endpoints.events.counties_list_helper counties_list = 2;</code>
     */
    private $counties_list;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $dma_name
     *     @type \Endpoints\Events\counties_list_helper[]|\Google\Protobuf\Internal\RepeatedField $counties_list
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Events::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string dma_name = 1;</code>
     * @return string
     */
    public function getDmaName()
    {
        return $this->dma_name;
    }

    /**
     * Generated from protobuf field <code>string dma_name = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setDmaName($var)
    {
        GPBUtil::checkString($var, True);
        $this->dma_name = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>repeated .endpoints.events.counties_list_helper counties_list = 2;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getCountiesList()
    {
        return $this->counties_list;
    }

    /**
     * Generated from protobuf field <code>repeated .endpoints.events.counties_list_helper counties_list = 2;</code>
     * @param \Endpoints\Events\counties_list_helper[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setCountiesList($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Endpoints\Events\counties_list_helper::class);
        $this->counties_list = $arr;

        return $this;
    }

}

