<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: events.proto

namespace Endpoints\Events;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 *When VNP want to get the all available geo, subject, entities (people or organizations name) and main subjects on the VNE,
 *it can use this endpoint.
 *Sample Request:
 *{
 *'requested_tag_type':'Entity',
 *'search_string':'ronaldo'
 *}
 *
 * Generated from protobuf message <code>endpoints.events.GetVNEDataRequest</code>
 */
class GetVNEDataRequest extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>int64 skip = 3;</code>
     */
    protected $skip = 0;
    /**
     * Generated from protobuf field <code>int64 limit = 4;</code>
     */
    protected $limit = 0;
    protected $requested_tag_type_oneof;
    protected $search_string_oneof;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $requested_tag_type
     *     @type string $search_string
     *     @type int|string $skip
     *     @type int|string $limit
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Events::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string requested_tag_type = 1;</code>
     * @return string
     */
    public function getRequestedTagType()
    {
        return $this->readOneof(1);
    }

    public function hasRequestedTagType()
    {
        return $this->hasOneof(1);
    }

    /**
     * Generated from protobuf field <code>string requested_tag_type = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setRequestedTagType($var)
    {
        GPBUtil::checkString($var, True);
        $this->writeOneof(1, $var);

        return $this;
    }

    /**
     * Generated from protobuf field <code>string search_string = 2;</code>
     * @return string
     */
    public function getSearchString()
    {
        return $this->readOneof(2);
    }

    public function hasSearchString()
    {
        return $this->hasOneof(2);
    }

    /**
     * Generated from protobuf field <code>string search_string = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setSearchString($var)
    {
        GPBUtil::checkString($var, True);
        $this->writeOneof(2, $var);

        return $this;
    }

    /**
     * Generated from protobuf field <code>int64 skip = 3;</code>
     * @return int|string
     */
    public function getSkip()
    {
        return $this->skip;
    }

    /**
     * Generated from protobuf field <code>int64 skip = 3;</code>
     * @param int|string $var
     * @return $this
     */
    public function setSkip($var)
    {
        GPBUtil::checkInt64($var);
        $this->skip = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int64 limit = 4;</code>
     * @return int|string
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Generated from protobuf field <code>int64 limit = 4;</code>
     * @param int|string $var
     * @return $this
     */
    public function setLimit($var)
    {
        GPBUtil::checkInt64($var);
        $this->limit = $var;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequestedTagTypeOneof()
    {
        return $this->whichOneof("requested_tag_type_oneof");
    }

    /**
     * @return string
     */
    public function getSearchStringOneof()
    {
        return $this->whichOneof("search_string_oneof");
    }

}

