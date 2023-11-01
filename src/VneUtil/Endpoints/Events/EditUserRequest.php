<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: events.proto

namespace Endpoints\Events;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 *1- User changes field or fields (zip codes, pref/not pref story types ....)
 *2- The VNP detects the changed field(s) and only sends a GRPC msg to VNE
 *3- The VNE overwrites the field(s). in other words, edit, add or remove things
 *For example if a user wants to edit his/her user name and zip code:
 *{
 * 'user_id': '503f3edc-c661-11ea-9bd7-c7df0d7a67c8',
 * 'user_name': 'Alex',
 * 'zip_code': '71005'
 *}
 *VNP changes his user name and zip code then notifies VNE with a GRPC message containing the user_id.
 *
 * Generated from protobuf message <code>endpoints.events.EditUserRequest</code>
 */
class EditUserRequest extends \Google\Protobuf\Internal\Message
{
    /**
     * Unique user ID for create new user (UUID)
     *
     * Generated from protobuf field <code>string user_id = 1;</code>
     */
    protected $user_id = '';
    /**
     *User Name
     *
     * Generated from protobuf field <code>repeated string news_markets = 2;</code>
     */
    private $news_markets;
    /**
     * Generated from protobuf field <code>string zip_code = 3;</code>
     */
    protected $zip_code = '';
    /**
     *User IP Address
     *
     * Generated from protobuf field <code>string ip_address = 6;</code>
     */
    protected $ip_address = '';
    /**
     *User favorite place
     *
     * Generated from protobuf field <code>repeated string pref_geo = 7;</code>
     */
    private $pref_geo;
    /**
     *User unfavorable place
     *
     * Generated from protobuf field <code>repeated string not_pref_geo = 8;</code>
     */
    private $not_pref_geo;
    /**
     *User favorite organization
     *
     * Generated from protobuf field <code>repeated string pref_people_organization = 9;</code>
     */
    private $pref_people_organization;
    /**
     *User unfavorable organization
     *
     * Generated from protobuf field <code>repeated string not_pref_people_organization = 10;</code>
     */
    private $not_pref_people_organization;
    /**
     * User favorite high level subject
     *
     * Generated from protobuf field <code>repeated string pref_highlevel_subject = 11;</code>
     */
    private $pref_highlevel_subject;
    /**
     *User unfavorable high level subject
     *
     * Generated from protobuf field <code>repeated string not_pref_highlevel_subject = 12;</code>
     */
    private $not_pref_highlevel_subject;
    /**
     *User favorite source entity
     *
     * Generated from protobuf field <code>repeated string pref_source_entity = 15;</code>
     */
    private $pref_source_entity;
    /**
     *User unfavorable source entity
     *
     * Generated from protobuf field <code>repeated string not_pref_source_entity = 16;</code>
     */
    private $not_pref_source_entity;
    /**
     *User personal interest
     *
     * Generated from protobuf field <code>repeated string pref_personal_interest = 17;</code>
     */
    private $pref_personal_interest;
    /**
     * Generated from protobuf field <code>repeated string pref_subjects = 20;</code>
     */
    private $pref_subjects;
    /**
     * Generated from protobuf field <code>repeated string not_pref_subjects = 21;</code>
     */
    private $not_pref_subjects;
    protected $gender_oneof;
    protected $birth_year_oneof;
    protected $preferred_lineup_duration_oneof;
    protected $home_county_oneof;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $user_id
     *           Unique user ID for create new user (UUID)
     *     @type string[]|\Google\Protobuf\Internal\RepeatedField $news_markets
     *          User Name
     *     @type string $zip_code
     *     @type int $gender
     *          User gender
     *     @type int $birth_year
     *          User birth year
     *     @type string $ip_address
     *          User IP Address
     *     @type string[]|\Google\Protobuf\Internal\RepeatedField $pref_geo
     *          User favorite place
     *     @type string[]|\Google\Protobuf\Internal\RepeatedField $not_pref_geo
     *          User unfavorable place
     *     @type string[]|\Google\Protobuf\Internal\RepeatedField $pref_people_organization
     *          User favorite organization
     *     @type string[]|\Google\Protobuf\Internal\RepeatedField $not_pref_people_organization
     *          User unfavorable organization
     *     @type string[]|\Google\Protobuf\Internal\RepeatedField $pref_highlevel_subject
     *           User favorite high level subject
     *     @type string[]|\Google\Protobuf\Internal\RepeatedField $not_pref_highlevel_subject
     *          User unfavorable high level subject
     *     @type string[]|\Google\Protobuf\Internal\RepeatedField $pref_source_entity
     *          User favorite source entity
     *     @type string[]|\Google\Protobuf\Internal\RepeatedField $not_pref_source_entity
     *          User unfavorable source entity
     *     @type string[]|\Google\Protobuf\Internal\RepeatedField $pref_personal_interest
     *          User personal interest
     *     @type int $preferred_lineup_duration
     *          Favorite lineup duration
     *     @type string $home_county
     *     @type string[]|\Google\Protobuf\Internal\RepeatedField $pref_subjects
     *     @type string[]|\Google\Protobuf\Internal\RepeatedField $not_pref_subjects
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Events::initOnce();
        parent::__construct($data);
    }

    /**
     * Unique user ID for create new user (UUID)
     *
     * Generated from protobuf field <code>string user_id = 1;</code>
     * @return string
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Unique user ID for create new user (UUID)
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
     *User Name
     *
     * Generated from protobuf field <code>repeated string news_markets = 2;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getNewsMarkets()
    {
        return $this->news_markets;
    }

    /**
     *User Name
     *
     * Generated from protobuf field <code>repeated string news_markets = 2;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setNewsMarkets($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::STRING);
        $this->news_markets = $arr;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string zip_code = 3;</code>
     * @return string
     */
    public function getZipCode()
    {
        return $this->zip_code;
    }

    /**
     * Generated from protobuf field <code>string zip_code = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setZipCode($var)
    {
        GPBUtil::checkString($var, True);
        $this->zip_code = $var;

        return $this;
    }

    /**
     *User gender
     *
     * Generated from protobuf field <code>int32 gender = 4;</code>
     * @return int
     */
    public function getGender()
    {
        return $this->readOneof(4);
    }

    public function hasGender()
    {
        return $this->hasOneof(4);
    }

    /**
     *User gender
     *
     * Generated from protobuf field <code>int32 gender = 4;</code>
     * @param int $var
     * @return $this
     */
    public function setGender($var)
    {
        GPBUtil::checkInt32($var);
        $this->writeOneof(4, $var);

        return $this;
    }

    /**
     *User birth year
     *
     * Generated from protobuf field <code>int32 birth_year = 5;</code>
     * @return int
     */
    public function getBirthYear()
    {
        return $this->readOneof(5);
    }

    public function hasBirthYear()
    {
        return $this->hasOneof(5);
    }

    /**
     *User birth year
     *
     * Generated from protobuf field <code>int32 birth_year = 5;</code>
     * @param int $var
     * @return $this
     */
    public function setBirthYear($var)
    {
        GPBUtil::checkInt32($var);
        $this->writeOneof(5, $var);

        return $this;
    }

    /**
     *User IP Address
     *
     * Generated from protobuf field <code>string ip_address = 6;</code>
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ip_address;
    }

    /**
     *User IP Address
     *
     * Generated from protobuf field <code>string ip_address = 6;</code>
     * @param string $var
     * @return $this
     */
    public function setIpAddress($var)
    {
        GPBUtil::checkString($var, True);
        $this->ip_address = $var;

        return $this;
    }

    /**
     *User favorite place
     *
     * Generated from protobuf field <code>repeated string pref_geo = 7;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getPrefGeo()
    {
        return $this->pref_geo;
    }

    /**
     *User favorite place
     *
     * Generated from protobuf field <code>repeated string pref_geo = 7;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setPrefGeo($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::STRING);
        $this->pref_geo = $arr;

        return $this;
    }

    /**
     *User unfavorable place
     *
     * Generated from protobuf field <code>repeated string not_pref_geo = 8;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getNotPrefGeo()
    {
        return $this->not_pref_geo;
    }

    /**
     *User unfavorable place
     *
     * Generated from protobuf field <code>repeated string not_pref_geo = 8;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setNotPrefGeo($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::STRING);
        $this->not_pref_geo = $arr;

        return $this;
    }

    /**
     *User favorite organization
     *
     * Generated from protobuf field <code>repeated string pref_people_organization = 9;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getPrefPeopleOrganization()
    {
        return $this->pref_people_organization;
    }

    /**
     *User favorite organization
     *
     * Generated from protobuf field <code>repeated string pref_people_organization = 9;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setPrefPeopleOrganization($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::STRING);
        $this->pref_people_organization = $arr;

        return $this;
    }

    /**
     *User unfavorable organization
     *
     * Generated from protobuf field <code>repeated string not_pref_people_organization = 10;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getNotPrefPeopleOrganization()
    {
        return $this->not_pref_people_organization;
    }

    /**
     *User unfavorable organization
     *
     * Generated from protobuf field <code>repeated string not_pref_people_organization = 10;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setNotPrefPeopleOrganization($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::STRING);
        $this->not_pref_people_organization = $arr;

        return $this;
    }

    /**
     * User favorite high level subject
     *
     * Generated from protobuf field <code>repeated string pref_highlevel_subject = 11;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getPrefHighlevelSubject()
    {
        return $this->pref_highlevel_subject;
    }

    /**
     * User favorite high level subject
     *
     * Generated from protobuf field <code>repeated string pref_highlevel_subject = 11;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setPrefHighlevelSubject($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::STRING);
        $this->pref_highlevel_subject = $arr;

        return $this;
    }

    /**
     *User unfavorable high level subject
     *
     * Generated from protobuf field <code>repeated string not_pref_highlevel_subject = 12;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getNotPrefHighlevelSubject()
    {
        return $this->not_pref_highlevel_subject;
    }

    /**
     *User unfavorable high level subject
     *
     * Generated from protobuf field <code>repeated string not_pref_highlevel_subject = 12;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setNotPrefHighlevelSubject($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::STRING);
        $this->not_pref_highlevel_subject = $arr;

        return $this;
    }

    /**
     *User favorite source entity
     *
     * Generated from protobuf field <code>repeated string pref_source_entity = 15;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getPrefSourceEntity()
    {
        return $this->pref_source_entity;
    }

    /**
     *User favorite source entity
     *
     * Generated from protobuf field <code>repeated string pref_source_entity = 15;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setPrefSourceEntity($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::STRING);
        $this->pref_source_entity = $arr;

        return $this;
    }

    /**
     *User unfavorable source entity
     *
     * Generated from protobuf field <code>repeated string not_pref_source_entity = 16;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getNotPrefSourceEntity()
    {
        return $this->not_pref_source_entity;
    }

    /**
     *User unfavorable source entity
     *
     * Generated from protobuf field <code>repeated string not_pref_source_entity = 16;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setNotPrefSourceEntity($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::STRING);
        $this->not_pref_source_entity = $arr;

        return $this;
    }

    /**
     *User personal interest
     *
     * Generated from protobuf field <code>repeated string pref_personal_interest = 17;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getPrefPersonalInterest()
    {
        return $this->pref_personal_interest;
    }

    /**
     *User personal interest
     *
     * Generated from protobuf field <code>repeated string pref_personal_interest = 17;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setPrefPersonalInterest($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::STRING);
        $this->pref_personal_interest = $arr;

        return $this;
    }

    /**
     *Favorite lineup duration
     *
     * Generated from protobuf field <code>int32 preferred_lineup_duration = 18;</code>
     * @return int
     */
    public function getPreferredLineupDuration()
    {
        return $this->readOneof(18);
    }

    public function hasPreferredLineupDuration()
    {
        return $this->hasOneof(18);
    }

    /**
     *Favorite lineup duration
     *
     * Generated from protobuf field <code>int32 preferred_lineup_duration = 18;</code>
     * @param int $var
     * @return $this
     */
    public function setPreferredLineupDuration($var)
    {
        GPBUtil::checkInt32($var);
        $this->writeOneof(18, $var);

        return $this;
    }

    /**
     * Generated from protobuf field <code>string home_county = 19;</code>
     * @return string
     */
    public function getHomeCounty()
    {
        return $this->readOneof(19);
    }

    public function hasHomeCounty()
    {
        return $this->hasOneof(19);
    }

    /**
     * Generated from protobuf field <code>string home_county = 19;</code>
     * @param string $var
     * @return $this
     */
    public function setHomeCounty($var)
    {
        GPBUtil::checkString($var, True);
        $this->writeOneof(19, $var);

        return $this;
    }

    /**
     * Generated from protobuf field <code>repeated string pref_subjects = 20;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getPrefSubjects()
    {
        return $this->pref_subjects;
    }

    /**
     * Generated from protobuf field <code>repeated string pref_subjects = 20;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setPrefSubjects($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::STRING);
        $this->pref_subjects = $arr;

        return $this;
    }

    /**
     * Generated from protobuf field <code>repeated string not_pref_subjects = 21;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getNotPrefSubjects()
    {
        return $this->not_pref_subjects;
    }

    /**
     * Generated from protobuf field <code>repeated string not_pref_subjects = 21;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setNotPrefSubjects($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::STRING);
        $this->not_pref_subjects = $arr;

        return $this;
    }

    /**
     * @return string
     */
    public function getGenderOneof()
    {
        return $this->whichOneof("gender_oneof");
    }

    /**
     * @return string
     */
    public function getBirthYearOneof()
    {
        return $this->whichOneof("birth_year_oneof");
    }

    /**
     * @return string
     */
    public function getPreferredLineupDurationOneof()
    {
        return $this->whichOneof("preferred_lineup_duration_oneof");
    }

    /**
     * @return string
     */
    public function getHomeCountyOneof()
    {
        return $this->whichOneof("home_county_oneof");
    }

}
