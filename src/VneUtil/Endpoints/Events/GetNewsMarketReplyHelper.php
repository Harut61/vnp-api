<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: events.proto

namespace Endpoints\Events;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>endpoints.events.GetNewsMarketReplyHelper</code>
 */
class GetNewsMarketReplyHelper extends \Google\Protobuf\Internal\Message
{
    /**
     *VNP id
     *
     * Generated from protobuf field <code>string vne_id = 1;</code>
     */
    protected $vne_id = '';
    /**
     *DMA name
     *
     * Generated from protobuf field <code>string news_market = 2;</code>
     */
    protected $news_market = '';
    /**
     * Generated from protobuf field <code>string parent = 4;</code>
     */
    protected $parent = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $vne_id
     *          VNP id
     *     @type string $news_market
     *          DMA name
     *     @type string $parent
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Events::initOnce();
        parent::__construct($data);
    }

    /**
     *VNP id
     *
     * Generated from protobuf field <code>string vne_id = 1;</code>
     * @return string
     */
    public function getVneId()
    {
        return $this->vne_id;
    }

    /**
     *VNP id
     *
     * Generated from protobuf field <code>string vne_id = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setVneId($var)
    {
        GPBUtil::checkString($var, True);
        $this->vne_id = $var;

        return $this;
    }

    /**
     *DMA name
     *
     * Generated from protobuf field <code>string news_market = 2;</code>
     * @return string
     */
    public function getNewsMarket()
    {
        return $this->news_market;
    }

    /**
     *DMA name
     *
     * Generated from protobuf field <code>string news_market = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setNewsMarket($var)
    {
        GPBUtil::checkString($var, True);
        $this->news_market = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string parent = 4;</code>
     * @return string
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Generated from protobuf field <code>string parent = 4;</code>
     * @param string $var
     * @return $this
     */
    public function setParent($var)
    {
        GPBUtil::checkString($var, True);
        $this->parent = $var;

        return $this;
    }

}
