<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: events.proto

namespace Endpoints\Events;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 *VNP can add DMA
 *Sample request body:
 *{'dma_name': 'Germany',
 * 'counties_id':[
 *'Hamburg',
 *'Munich',
 *'Koln'],
 * 'created_by':'William'
 *}
 *
 * Generated from protobuf message <code>endpoints.events.AddNewsMarketRequest</code>
 */
class AddNewsMarketRequest extends \Google\Protobuf\Internal\Message
{
    /**
     *Name of the new DMA
     *
     * Generated from protobuf field <code>string news_market_name = 1;</code>
     */
    protected $news_market_name = '';
    /**
     *List of id of counties for the DMA
     *
     * Generated from protobuf field <code>repeated string counties_id = 2;</code>
     */
    private $counties_id;
    /**
     *The person who created the DMA
     *
     * Generated from protobuf field <code>string created_by = 3;</code>
     */
    protected $created_by = '';
    /**
     * Generated from protobuf field <code>string vne_id = 4;</code>
     */
    protected $vne_id = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $news_market_name
     *          Name of the new DMA
     *     @type string[]|\Google\Protobuf\Internal\RepeatedField $counties_id
     *          List of id of counties for the DMA
     *     @type string $created_by
     *          The person who created the DMA
     *     @type string $vne_id
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Events::initOnce();
        parent::__construct($data);
    }

    /**
     *Name of the new DMA
     *
     * Generated from protobuf field <code>string news_market_name = 1;</code>
     * @return string
     */
    public function getNewsMarketName()
    {
        return $this->news_market_name;
    }

    /**
     *Name of the new DMA
     *
     * Generated from protobuf field <code>string news_market_name = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setNewsMarketName($var)
    {
        GPBUtil::checkString($var, True);
        $this->news_market_name = $var;

        return $this;
    }

    /**
     *List of id of counties for the DMA
     *
     * Generated from protobuf field <code>repeated string counties_id = 2;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getCountiesId()
    {
        return $this->counties_id;
    }

    /**
     *List of id of counties for the DMA
     *
     * Generated from protobuf field <code>repeated string counties_id = 2;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setCountiesId($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::STRING);
        $this->counties_id = $arr;

        return $this;
    }

    /**
     *The person who created the DMA
     *
     * Generated from protobuf field <code>string created_by = 3;</code>
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     *The person who created the DMA
     *
     * Generated from protobuf field <code>string created_by = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setCreatedBy($var)
    {
        GPBUtil::checkString($var, True);
        $this->created_by = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string vne_id = 4;</code>
     * @return string
     */
    public function getVneId()
    {
        return $this->vne_id;
    }

    /**
     * Generated from protobuf field <code>string vne_id = 4;</code>
     * @param string $var
     * @return $this
     */
    public function setVneId($var)
    {
        GPBUtil::checkString($var, True);
        $this->vne_id = $var;

        return $this;
    }

}

