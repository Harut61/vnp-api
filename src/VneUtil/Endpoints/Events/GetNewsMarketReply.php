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
 * 'dma_list': [
 * {'VNP_id': 669, 'DMA_name': 'MADISON'},
 * {'VNP_id': 628, 'DMA_name': 'MONROE-EL DORADO'},
 * {'VNP_id': 632, 'DMA_name': 'PADUCAH-CAPE GIRARDEAU-HARRISBURG'},
 * {'VNP_id': 504, 'DMA_name': 'PHILADELPHIA'},
 * {'VNP_id': 657, 'DMA_name': 'SHERMAN-ADA'}
 * ],
 * 'remaining_dma': 1}
 *
 * Generated from protobuf message <code>endpoints.events.GetNewsMarketReply</code>
 */
class GetNewsMarketReply extends \Google\Protobuf\Internal\Message
{
    /**
     *DMA list contain vnp id and dma name
     *
     * Generated from protobuf field <code>repeated .endpoints.events.GetNewsMarketReplyHelper news_market_list = 1;</code>
     */
    private $news_market_list;
    /**
     * Generated from protobuf field <code>string status = 3;</code>
     */
    protected $status = '';
    protected $remaining_news_market_oneof;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \Endpoints\Events\GetNewsMarketReplyHelper[]|\Google\Protobuf\Internal\RepeatedField $news_market_list
     *          DMA list contain vnp id and dma name
     *     @type int $remaining_news_market
     *     @type string $status
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Events::initOnce();
        parent::__construct($data);
    }

    /**
     *DMA list contain vnp id and dma name
     *
     * Generated from protobuf field <code>repeated .endpoints.events.GetNewsMarketReplyHelper news_market_list = 1;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getNewsMarketList()
    {
        return $this->news_market_list;
    }

    /**
     *DMA list contain vnp id and dma name
     *
     * Generated from protobuf field <code>repeated .endpoints.events.GetNewsMarketReplyHelper news_market_list = 1;</code>
     * @param \Endpoints\Events\GetNewsMarketReplyHelper[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setNewsMarketList($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Endpoints\Events\GetNewsMarketReplyHelper::class);
        $this->news_market_list = $arr;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 remaining_news_market = 2;</code>
     * @return int
     */
    public function getRemainingNewsMarket()
    {
        return $this->readOneof(2);
    }

    public function hasRemainingNewsMarket()
    {
        return $this->hasOneof(2);
    }

    /**
     * Generated from protobuf field <code>int32 remaining_news_market = 2;</code>
     * @param int $var
     * @return $this
     */
    public function setRemainingNewsMarket($var)
    {
        GPBUtil::checkInt32($var);
        $this->writeOneof(2, $var);

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

    /**
     * @return string
     */
    public function getRemainingNewsMarketOneof()
    {
        return $this->whichOneof("remaining_news_market_oneof");
    }

}

