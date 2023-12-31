<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: events.proto

namespace Endpoints\Events;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 *VNP can send a request to get the number of stories for a user which has a reasonable score for the user and
 *number of stories that the user can see in the lineups.
 *Sample Request body:
 *{
 *'user_id':'8557f811-69e9-4379-912f-4ddbacc3da00',
 *}
 *
 * Generated from protobuf message <code>endpoints.events.GetUserStoryRequest</code>
 */
class GetUserStoryRequest extends \Google\Protobuf\Internal\Message
{
    /**
     *Unique user ID (UUID)
     *
     * Generated from protobuf field <code>string user_id = 1;</code>
     */
    protected $user_id = '';
    /**
     *Geographic coordinate that specifies the position latitude
     *
     * Generated from protobuf field <code>float latitude = 2;</code>
     */
    protected $latitude = 0.0;
    /**
     *Geographic coordinate that specifies the position longitude
     *
     * Generated from protobuf field <code>float longitude = 3;</code>
     */
    protected $longitude = 0.0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $user_id
     *          Unique user ID (UUID)
     *     @type float $latitude
     *          Geographic coordinate that specifies the position latitude
     *     @type float $longitude
     *          Geographic coordinate that specifies the position longitude
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Events::initOnce();
        parent::__construct($data);
    }

    /**
     *Unique user ID (UUID)
     *
     * Generated from protobuf field <code>string user_id = 1;</code>
     * @return string
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     *Unique user ID (UUID)
     *
     * Generated from protobuf field <code>string user_id = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setUserId($var)
    {
        GPBUtil::checkString($var, True);
        $this->user_id = $var;

        return $this;
    }

    /**
     *Geographic coordinate that specifies the position latitude
     *
     * Generated from protobuf field <code>float latitude = 2;</code>
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     *Geographic coordinate that specifies the position latitude
     *
     * Generated from protobuf field <code>float latitude = 2;</code>
     * @param float $var
     * @return $this
     */
    public function setLatitude($var)
    {
        GPBUtil::checkFloat($var);
        $this->latitude = $var;

        return $this;
    }

    /**
     *Geographic coordinate that specifies the position longitude
     *
     * Generated from protobuf field <code>float longitude = 3;</code>
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     *Geographic coordinate that specifies the position longitude
     *
     * Generated from protobuf field <code>float longitude = 3;</code>
     * @param float $var
     * @return $this
     */
    public function setLongitude($var)
    {
        GPBUtil::checkFloat($var);
        $this->longitude = $var;

        return $this;
    }

}

