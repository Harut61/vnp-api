<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: events.proto

namespace Endpoints\Events;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>endpoints.events.AddSeriesRequest</code>
 */
class AddSeriesRequest extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string series_name = 1;</code>
     */
    protected $series_name = '';
    /**
     * Generated from protobuf field <code>string show_name = 2;</code>
     */
    protected $show_name = '';
    /**
     * Generated from protobuf field <code>string source_name = 3;</code>
     */
    protected $source_name = '';
    /**
     * Generated from protobuf field <code>string local_news_market = 4;</code>
     */
    protected $local_news_market = '';
    /**
     * Generated from protobuf field <code>int32 local_timezone = 5;</code>
     */
    protected $local_timezone = 0;
    /**
     * Generated from protobuf field <code>string telecast_time = 6;</code>
     */
    protected $telecast_time = '';
    /**
     * Generated from protobuf field <code>string created_by = 7;</code>
     */
    protected $created_by = '';
    /**
     * Generated from protobuf field <code>string vnp_id = 8;</code>
     */
    protected $vnp_id = '';
    /**
     * Generated from protobuf field <code>string delay_news_market_list = 9;</code>
     */
    protected $delay_news_market_list = '';
    /**
     * Generated from protobuf field <code>string show_id = 10;</code>
     */
    protected $show_id = '';
    /**
     * Generated from protobuf field <code>float length = 11;</code>
     */
    protected $length = 0.0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $series_name
     *     @type string $show_name
     *     @type string $source_name
     *     @type string $local_news_market
     *     @type int $local_timezone
     *     @type string $telecast_time
     *     @type string $created_by
     *     @type string $vnp_id
     *     @type string $delay_news_market_list
     *     @type string $show_id
     *     @type float $length
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Events::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string series_name = 1;</code>
     * @return string
     */
    public function getSeriesName()
    {
        return $this->series_name;
    }

    /**
     * Generated from protobuf field <code>string series_name = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setSeriesName($var)
    {
        GPBUtil::checkString($var, True);
        $this->series_name = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string show_name = 2;</code>
     * @return string
     */
    public function getShowName()
    {
        return $this->show_name;
    }

    /**
     * Generated from protobuf field <code>string show_name = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setShowName($var)
    {
        GPBUtil::checkString($var, True);
        $this->show_name = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string source_name = 3;</code>
     * @return string
     */
    public function getSourceName()
    {
        return $this->source_name;
    }

    /**
     * Generated from protobuf field <code>string source_name = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setSourceName($var)
    {
        GPBUtil::checkString($var, True);
        $this->source_name = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string local_news_market = 4;</code>
     * @return string
     */
    public function getLocalNewsMarket()
    {
        return $this->local_news_market;
    }

    /**
     * Generated from protobuf field <code>string local_news_market = 4;</code>
     * @param string $var
     * @return $this
     */
    public function setLocalNewsMarket($var)
    {
        GPBUtil::checkString($var, True);
        $this->local_news_market = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 local_timezone = 5;</code>
     * @return int
     */
    public function getLocalTimezone()
    {
        return $this->local_timezone;
    }

    /**
     * Generated from protobuf field <code>int32 local_timezone = 5;</code>
     * @param int $var
     * @return $this
     */
    public function setLocalTimezone($var)
    {
        GPBUtil::checkInt32($var);
        $this->local_timezone = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string telecast_time = 6;</code>
     * @return string
     */
    public function getTelecastTime()
    {
        return $this->telecast_time;
    }

    /**
     * Generated from protobuf field <code>string telecast_time = 6;</code>
     * @param string $var
     * @return $this
     */
    public function setTelecastTime($var)
    {
        GPBUtil::checkString($var, True);
        $this->telecast_time = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string created_by = 7;</code>
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * Generated from protobuf field <code>string created_by = 7;</code>
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
     * Generated from protobuf field <code>string vnp_id = 8;</code>
     * @return string
     */
    public function getVnpId()
    {
        return $this->vnp_id;
    }

    /**
     * Generated from protobuf field <code>string vnp_id = 8;</code>
     * @param string $var
     * @return $this
     */
    public function setVnpId($var)
    {
        GPBUtil::checkString($var, True);
        $this->vnp_id = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string delay_news_market_list = 9;</code>
     * @return string
     */
    public function getDelayNewsMarketList()
    {
        return $this->delay_news_market_list;
    }

    /**
     * Generated from protobuf field <code>string delay_news_market_list = 9;</code>
     * @param string $var
     * @return $this
     */
    public function setDelayNewsMarketList($var)
    {
        GPBUtil::checkString($var, True);
        $this->delay_news_market_list = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string show_id = 10;</code>
     * @return string
     */
    public function getShowId()
    {
        return $this->show_id;
    }

    /**
     * Generated from protobuf field <code>string show_id = 10;</code>
     * @param string $var
     * @return $this
     */
    public function setShowId($var)
    {
        GPBUtil::checkString($var, True);
        $this->show_id = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>float length = 11;</code>
     * @return float
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Generated from protobuf field <code>float length = 11;</code>
     * @param float $var
     * @return $this
     */
    public function setLength($var)
    {
        GPBUtil::checkFloat($var);
        $this->length = $var;

        return $this;
    }

}

