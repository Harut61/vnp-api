<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: events.proto

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
 * Generated from protobuf message <code>EditStoryRequest</code>
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
     *Story source entity
     *
     * Generated from protobuf field <code>string source_entity = 2;</code>
     */
    protected $source_entity = '';
    /**
     *Story show name
     *
     * Generated from protobuf field <code>string show_name = 3;</code>
     */
    protected $show_name = '';
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
     * Generated from protobuf field <code>string story_start_time = 12;</code>
     */
    protected $story_start_time = '';
    /**
     *Story lede duration
     *
     * Generated from protobuf field <code>float lede_duration = 13;</code>
     */
    protected $lede_duration = 0.0;
    /**
     *Story end time
     *
     * Generated from protobuf field <code>string story_end_time = 14;</code>
     */
    protected $story_end_time = '';
    /**
     *Show publication date time
     *
     * Generated from protobuf field <code>string show_publication_datetime = 15;</code>
     */
    protected $show_publication_datetime = '';
    protected $frame_rate_oneof;
    protected $show_length_oneof;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $story_id
     *          Unique Story ID (UUID)
     *     @type string $source_entity
     *          Story source entity
     *     @type string $show_name
     *          Story show name
     *     @type string $video_url
     *          Story video URL
     *     @type float $frame_rate
     *          Story frame rate
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
     *     @type string $story_start_time
     *          Story start time
     *     @type float $lede_duration
     *          Story lede duration
     *     @type string $story_end_time
     *          Story end time
     *     @type string $show_publication_datetime
     *          Show publication date time
     *     @type float $show_length
     *          Show length
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
     *Story source entity
     *
     * Generated from protobuf field <code>string source_entity = 2;</code>
     * @return string
     */
    public function getSourceEntity()
    {
        return $this->source_entity;
    }

    /**
     *Story source entity
     *
     * Generated from protobuf field <code>string source_entity = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setSourceEntity($var)
    {
        GPBUtil::checkString($var, True);
        $this->source_entity = $var;

        return $this;
    }

    /**
     *Story show name
     *
     * Generated from protobuf field <code>string show_name = 3;</code>
     * @return string
     */
    public function getShowName()
    {
        return $this->show_name;
    }

    /**
     *Story show name
     *
     * Generated from protobuf field <code>string show_name = 3;</code>
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
     *Story frame rate
     *
     * Generated from protobuf field <code>float frame_rate = 5;</code>
     * @return float
     */
    public function getFrameRate()
    {
        return $this->readOneof(5);
    }

    public function hasFrameRate()
    {
        return $this->hasOneof(5);
    }

    /**
     *Story frame rate
     *
     * Generated from protobuf field <code>float frame_rate = 5;</code>
     * @param float $var
     * @return $this
     */
    public function setFrameRate($var)
    {
        GPBUtil::checkFloat($var);
        $this->writeOneof(5, $var);

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
     * Generated from protobuf field <code>string story_start_time = 12;</code>
     * @return string
     */
    public function getStoryStartTime()
    {
        return $this->story_start_time;
    }

    /**
     *Story start time
     *
     * Generated from protobuf field <code>string story_start_time = 12;</code>
     * @param string $var
     * @return $this
     */
    public function setStoryStartTime($var)
    {
        GPBUtil::checkString($var, True);
        $this->story_start_time = $var;

        return $this;
    }

    /**
     *Story lede duration
     *
     * Generated from protobuf field <code>float lede_duration = 13;</code>
     * @return float
     */
    public function getLedeDuration()
    {
        return $this->lede_duration;
    }

    /**
     *Story lede duration
     *
     * Generated from protobuf field <code>float lede_duration = 13;</code>
     * @param float $var
     * @return $this
     */
    public function setLedeDuration($var)
    {
        GPBUtil::checkFloat($var);
        $this->lede_duration = $var;

        return $this;
    }

    /**
     *Story end time
     *
     * Generated from protobuf field <code>string story_end_time = 14;</code>
     * @return string
     */
    public function getStoryEndTime()
    {
        return $this->story_end_time;
    }

    /**
     *Story end time
     *
     * Generated from protobuf field <code>string story_end_time = 14;</code>
     * @param string $var
     * @return $this
     */
    public function setStoryEndTime($var)
    {
        GPBUtil::checkString($var, True);
        $this->story_end_time = $var;

        return $this;
    }

    /**
     *Show publication date time
     *
     * Generated from protobuf field <code>string show_publication_datetime = 15;</code>
     * @return string
     */
    public function getShowPublicationDatetime()
    {
        return $this->show_publication_datetime;
    }

    /**
     *Show publication date time
     *
     * Generated from protobuf field <code>string show_publication_datetime = 15;</code>
     * @param string $var
     * @return $this
     */
    public function setShowPublicationDatetime($var)
    {
        GPBUtil::checkString($var, True);
        $this->show_publication_datetime = $var;

        return $this;
    }

    /**
     *Show length
     *
     * Generated from protobuf field <code>float show_length = 16;</code>
     * @return float
     */
    public function getShowLength()
    {
        return $this->readOneof(16);
    }

    public function hasShowLength()
    {
        return $this->hasOneof(16);
    }

    /**
     *Show length
     *
     * Generated from protobuf field <code>float show_length = 16;</code>
     * @param float $var
     * @return $this
     */
    public function setShowLength($var)
    {
        GPBUtil::checkFloat($var);
        $this->writeOneof(16, $var);

        return $this;
    }

    /**
     * @return string
     */
    public function getFrameRateOneof()
    {
        return $this->whichOneof("frame_rate_oneof");
    }

    /**
     * @return string
     */
    public function getShowLengthOneof()
    {
        return $this->whichOneof("show_length_oneof");
    }

}

