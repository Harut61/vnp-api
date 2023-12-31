<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: events.proto

namespace Endpoints\Events;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 *1- VNP gets the story's information and show them to the Marker
 *2- Marker changes field or fields (title, story type ....)
 *3- the VNP detects the changed field(s) and sends a msg containing story_id and changed fields to VNE
 *4- The VNE overwrites the field(s). in other words, edit, add or remove things
 *For example if VNP wants to edit title and type of a story:
 *"{
 * 'story_id': 'f31ce620-ac1e-4a1b-aa14-82a1ec3bd417',
 * 'title': 'D.C. Mayor did something',
 * 'story_type': 'SN'
 *}"
 *VNP changes title and story_type then notifies VNE with a GRPC message containing the story_id.
 *
 * Generated from protobuf message <code>endpoints.events.EditStoryRequest</code>
 */
class EditStoryRequest extends \Google\Protobuf\Internal\Message
{
    /**
     *Unique Story ID (UUID)
     *
     * Generated from protobuf field <code>string story_id = 1;</code>
     */
    protected $story_id = '';
    /**
     *Story video URL
     *
     * Generated from protobuf field <code>string video_url = 4;</code>
     */
    protected $video_url = '';
    /**
     *Story title
     *
     * Generated from protobuf field <code>string title = 6;</code>
     */
    protected $title = '';
    /**
     *Story type
     *
     * Generated from protobuf field <code>string story_type = 7;</code>
     */
    protected $story_type = '';
    /**
     *Story high level subjects
     *
     * Generated from protobuf field <code>string story_highlevel_subjects = 8;</code>
     */
    protected $story_highlevel_subjects = '';
    /**
     *Story rank
     *
     * Generated from protobuf field <code>int32 story_rank = 9;</code>
     */
    protected $story_rank = 0;
    /**
     *Lede story subtitle text
     *
     * Generated from protobuf field <code>string lede_subtitle_text = 10;</code>
     */
    protected $lede_subtitle_text = '';
    /**
     *Rest story subtitle text
     *
     * Generated from protobuf field <code>string rest_story_subtitle_text = 11;</code>
     */
    protected $rest_story_subtitle_text = '';
    /**
     *Story start time
     *
     * Generated from protobuf field <code>string story_start = 12;</code>
     */
    protected $story_start = '';
    /**
     *Story lede duration
     *
     * Generated from protobuf field <code>float lede_end = 13;</code>
     */
    protected $lede_end = 0.0;
    /**
     *Story end time
     *
     * Generated from protobuf field <code>string story_end = 14;</code>
     */
    protected $story_end = '';
    /**
     * Generated from protobuf field <code>string story_video_id = 15;</code>
     */
    protected $story_video_id = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $story_id
     *          Unique Story ID (UUID)
     *     @type string $video_url
     *          Story video URL
     *     @type string $title
     *          Story title
     *     @type string $story_type
     *          Story type
     *     @type string $story_highlevel_subjects
     *          Story high level subjects
     *     @type int $story_rank
     *          Story rank
     *     @type string $lede_subtitle_text
     *          Lede story subtitle text
     *     @type string $rest_story_subtitle_text
     *          Rest story subtitle text
     *     @type string $story_start
     *          Story start time
     *     @type float $lede_end
     *          Story lede duration
     *     @type string $story_end
     *          Story end time
     *     @type string $story_video_id
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Events::initOnce();
        parent::__construct($data);
    }

    /**
     *Unique Story ID (UUID)
     *
     * Generated from protobuf field <code>string story_id = 1;</code>
     * @return string
     */
    public function getStoryId()
    {
        return $this->story_id;
    }

    /**
     *Unique Story ID (UUID)
     *
     * Generated from protobuf field <code>string story_id = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setStoryId($var)
    {
        GPBUtil::checkString($var, True);
        $this->story_id = $var;

        return $this;
    }

    /**
     *Story video URL
     *
     * Generated from protobuf field <code>string video_url = 4;</code>
     * @return string
     */
    public function getVideoUrl()
    {
        return $this->video_url;
    }

    /**
     *Story video URL
     *
     * Generated from protobuf field <code>string video_url = 4;</code>
     * @param string $var
     * @return $this
     */
    public function setVideoUrl($var)
    {
        GPBUtil::checkString($var, True);
        $this->video_url = $var;

        return $this;
    }

    /**
     *Story title
     *
     * Generated from protobuf field <code>string title = 6;</code>
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *Story title
     *
     * Generated from protobuf field <code>string title = 6;</code>
     * @param string $var
     * @return $this
     */
    public function setTitle($var)
    {
        GPBUtil::checkString($var, True);
        $this->title = $var;

        return $this;
    }

    /**
     *Story type
     *
     * Generated from protobuf field <code>string story_type = 7;</code>
     * @return string
     */
    public function getStoryType()
    {
        return $this->story_type;
    }

    /**
     *Story type
     *
     * Generated from protobuf field <code>string story_type = 7;</code>
     * @param string $var
     * @return $this
     */
    public function setStoryType($var)
    {
        GPBUtil::checkString($var, True);
        $this->story_type = $var;

        return $this;
    }

    /**
     *Story high level subjects
     *
     * Generated from protobuf field <code>string story_highlevel_subjects = 8;</code>
     * @return string
     */
    public function getStoryHighlevelSubjects()
    {
        return $this->story_highlevel_subjects;
    }

    /**
     *Story high level subjects
     *
     * Generated from protobuf field <code>string story_highlevel_subjects = 8;</code>
     * @param string $var
     * @return $this
     */
    public function setStoryHighlevelSubjects($var)
    {
        GPBUtil::checkString($var, True);
        $this->story_highlevel_subjects = $var;

        return $this;
    }

    /**
     *Story rank
     *
     * Generated from protobuf field <code>int32 story_rank = 9;</code>
     * @return int
     */
    public function getStoryRank()
    {
        return $this->story_rank;
    }

    /**
     *Story rank
     *
     * Generated from protobuf field <code>int32 story_rank = 9;</code>
     * @param int $var
     * @return $this
     */
    public function setStoryRank($var)
    {
        GPBUtil::checkInt32($var);
        $this->story_rank = $var;

        return $this;
    }

    /**
     *Lede story subtitle text
     *
     * Generated from protobuf field <code>string lede_subtitle_text = 10;</code>
     * @return string
     */
    public function getLedeSubtitleText()
    {
        return $this->lede_subtitle_text;
    }

    /**
     *Lede story subtitle text
     *
     * Generated from protobuf field <code>string lede_subtitle_text = 10;</code>
     * @param string $var
     * @return $this
     */
    public function setLedeSubtitleText($var)
    {
        GPBUtil::checkString($var, True);
        $this->lede_subtitle_text = $var;

        return $this;
    }

    /**
     *Rest story subtitle text
     *
     * Generated from protobuf field <code>string rest_story_subtitle_text = 11;</code>
     * @return string
     */
    public function getRestStorySubtitleText()
    {
        return $this->rest_story_subtitle_text;
    }

    /**
     *Rest story subtitle text
     *
     * Generated from protobuf field <code>string rest_story_subtitle_text = 11;</code>
     * @param string $var
     * @return $this
     */
    public function setRestStorySubtitleText($var)
    {
        GPBUtil::checkString($var, True);
        $this->rest_story_subtitle_text = $var;

        return $this;
    }

    /**
     *Story start time
     *
     * Generated from protobuf field <code>string story_start = 12;</code>
     * @return string
     */
    public function getStoryStart()
    {
        return $this->story_start;
    }

    /**
     *Story start time
     *
     * Generated from protobuf field <code>string story_start = 12;</code>
     * @param string $var
     * @return $this
     */
    public function setStoryStart($var)
    {
        GPBUtil::checkString($var, True);
        $this->story_start = $var;

        return $this;
    }

    /**
     *Story lede duration
     *
     * Generated from protobuf field <code>float lede_end = 13;</code>
     * @return float
     */
    public function getLedeEnd()
    {
        return $this->lede_end;
    }

    /**
     *Story lede duration
     *
     * Generated from protobuf field <code>float lede_end = 13;</code>
     * @param float $var
     * @return $this
     */
    public function setLedeEnd($var)
    {
        GPBUtil::checkFloat($var);
        $this->lede_end = $var;

        return $this;
    }

    /**
     *Story end time
     *
     * Generated from protobuf field <code>string story_end = 14;</code>
     * @return string
     */
    public function getStoryEnd()
    {
        return $this->story_end;
    }

    /**
     *Story end time
     *
     * Generated from protobuf field <code>string story_end = 14;</code>
     * @param string $var
     * @return $this
     */
    public function setStoryEnd($var)
    {
        GPBUtil::checkString($var, True);
        $this->story_end = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string story_video_id = 15;</code>
     * @return string
     */
    public function getStoryVideoId()
    {
        return $this->story_video_id;
    }

    /**
     * Generated from protobuf field <code>string story_video_id = 15;</code>
     * @param string $var
     * @return $this
     */
    public function setStoryVideoId($var)
    {
        GPBUtil::checkString($var, True);
        $this->story_video_id = $var;

        return $this;
    }

}

