<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: events.proto

namespace Endpoints\Events;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * VNP can update the existing DMAs
 *{
 * 'county_id': '4385125b-dd1e-3025-880f-3311517cc8d5',
 * 'new_dma:'Los Angeles'
 *}
 *
 * Generated from protobuf message <code>endpoints.events.UpdateDMARequest</code>
 */
class UpdateDMARequest extends \Google\Protobuf\Internal\Message
{
    /**
     * Id of target county
     *
     * Generated from protobuf field <code>string county_id = 1;</code>
     */
    protected $county_id = '';
    /**
     * The new DMA
     *
     * Generated from protobuf field <code>string new_dma = 2;</code>
     */
    protected $new_dma = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $county_id
     *           Id of target county
     *     @type string $new_dma
     *           The new DMA
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Events::initOnce();
        parent::__construct($data);
    }

    /**
     * Id of target county
     *
     * Generated from protobuf field <code>string county_id = 1;</code>
     * @return string
     */
    public function getCountyId()
    {
        return $this->county_id;
    }

    /**
     * Id of target county
     *
     * Generated from protobuf field <code>string county_id = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setCountyId($var)
    {
        GPBUtil::checkString($var, True);
        $this->county_id = $var;

        return $this;
    }

    /**
     * The new DMA
     *
     * Generated from protobuf field <code>string new_dma = 2;</code>
     * @return string
     */
    public function getNewDma()
    {
        return $this->new_dma;
    }

    /**
     * The new DMA
     *
     * Generated from protobuf field <code>string new_dma = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setNewDma($var)
    {
        GPBUtil::checkString($var, True);
        $this->new_dma = $var;

        return $this;
    }

}

