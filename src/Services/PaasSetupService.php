<?php
/**
 * Created by PhpStorm.
 * User: peanutsquare-p1
 * Date: 29-11-2020
 * Time: 00:12
 */

namespace App\Services;


use App\Entity\AdminRoles;
use App\Entity\AppSetting;
use App\Entity\TimeZone;
use App\Entity\TranscodingProfile;
use App\Entity\TranscodingProfileOption;
use App\Enums\AppSettingEnum;
use App\Enums\UserRoleEnum;
use Doctrine\ORM\EntityManagerInterface;

class PaasSetupService
{
    public $profileOptions = [
        [
            "title" => "FHD – 1080p",
            "desc" => "FHD (Full HD)",
            "fps" => 30,
            "audioCodec" => "AAC",
            "videoWidth" => 1920,
            "videoHeight" => 1080,
            "videoBitrate" => 4000000,
            "audioBitrate" => 128000,
            "profile" => "HIGH",
            "videoCodec" => "H_264"

        ],
        [
            "title" => "HD – 720p",
            "desc" => "SHD (Standard HD)",
            "fps" => 30,
            "audioCodec" => "AAC",
            "videoWidth" => 1280,
            "videoHeight" => 720,
            "videoBitrate" => 1024000,
            "audioBitrate" => 128000,
            "profile" => "HIGH",
            "videoCodec" => "H_264"

        ],
        [
            "title" => "SD – 540p",
            "desc" => "FWVGA (Full Wide Video Graphics Array)",
            "fps" => 30,
            "audioCodec" => "AAC",
            "videoWidth" => 960,
            "videoHeight" => 540,
            "videoBitrate" => 1500000,
            "audioBitrate" => 128000,
            "profile" => "HIGH",
            "videoCodec" => "H_264"

        ],
        [
            "title" => "LD – 360p",
            "desc" => "nHD (Ninth of High Definition)",
            "fps" => 30,
            "audioCodec" => "AAC",
            "videoWidth" => 640,
            "videoHeight" => 360,
            "videoBitrate" => 900000,
            "audioBitrate" => 128000,
            "profile" => "HIGH",
            "videoCodec" => "H_264"

        ],
        [
            "title" => "ULD – 240p",
            "desc" => "WQVGA (Wide Quarter Video Graphics Array)",
            "fps" => 30,
            "audioCodec" => "AAC",
            "videoWidth" => 426,
            "videoHeight" => 240,
            "videoBitrate" => 300000,
            "audioBitrate" => 128000,
            "profile" => "HIGH",
            "videoCodec" => "H_264"

        ]
    ];

    /**
     * Eastern Time    America/New_York
     * Central Time    America/Chicago
     *  Mountain Time   America/Denver
     *  Mountain Time (no DST) America/Phoenix
     *  Pacific Time    America/Los_Angeles
     *  Alaska Time America/Anchorage
     *  Hawaii-Aleutian America/Adak
     *  Hawaii-Aleutian Time (no DST) Pacific/Honolulu
     *
     * @var array
     */
    public $defaultTimeZone = [
        [
            "title" => "Atlantic Time Zone",
            "standardTime" => "-04:00",
            "dayLightSavingTime" => "-03:00",
            "phpTimeZone" => "America/Anchorage",
            "phpTimeZoneDLS" => ""
        ],
        [
            "title" => "Eastern Time Zone",
            "standardTime" => "-05:00",
            "dayLightSavingTime" => "-04:00",
            "phpTimeZone" => "America/New_York",
            "phpTimeZoneDLS" => ""
        ],
        [
            "title" => "Central Time Zone",
            "standardTime" => "-06:00",
            "dayLightSavingTime" => "-05:00",
            "phpTimeZone" => "America/Chicago",
            "phpTimeZoneDLS" => ""
        ],
        [
            "title" => "Mountain Time Zone",
            "standardTime" => "-07:00",
            "dayLightSavingTime" => "-06:00",
            "phpTimeZone" => "America/Denver",
            "phpTimeZoneDLS" => "America/Phoenix"
        ],
        [
            "title" => "Pacific Time Zone",
            "standardTime" => "-08:00",
            "dayLightSavingTime" => "-07:00",
            "phpTimeZone" => "America/Los_Angeles",
            "phpTimeZoneDLS" => ""
        ],
        [
            "title" => "Hawaii–Aleutian Time Zone",
            "standardTime" => "-10:00",
            "dayLightSavingTime" => "-09:00",
            "phpTimeZone" => "America/Adak",
            "phpTimeZoneDLS" => "Pacific/Honolulu"
        ]
    ];

    public $defaultAppParams = [
        [
            "title" => AppSettingEnum::TRANSCODING_PARALLEL,
            "paramValue" => "5"
        ],
        [
            "title" => AppSettingEnum::TRANSCODING_PER_DAY,
            "paramValue" => "10"
        ],
        [
            "title" => AppSettingEnum::LEDE_LENGTH,
            "paramValue" => null
        ],
        [
            "title" => AppSettingEnum::STORY_LENGTH,
            "paramValue" => null
        ],
        [
            "title" => AppSettingEnum::LEDE_END,
            "paramValue" => "5"
        ],
        [
            "title" => AppSettingEnum::STORY_END,
            "paramValue" => "5"
        ]
    ];

    public $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createDefaultTranscodingProfile()
    {

        $transcodingProfileOptionRepo = $this->entityManager->getRepository(TranscodingProfileOption::class);
        $transcodingProfileRepo = $this->entityManager->getRepository(TranscodingProfile::class);
        $res =[];

        foreach ($this->profileOptions as $profileOption) {

            /** @var TranscodingProfileOption $profileOptionExist */
            $profileOptionExist = $transcodingProfileOptionRepo->findOneBy(["title" => $profileOption["title"]]);
            if ($profileOptionExist) {
                continue;
            }

            $transcodingProfileOption = new TranscodingProfileOption();
            $transcodingProfileOption->setTitle($profileOption["title"]);
            $transcodingProfileOption->description = $profileOption["desc"];
            $transcodingProfileOption->fps = $profileOption["fps"];
            $transcodingProfileOption->videoWidth = $profileOption["videoWidth"];
            $transcodingProfileOption->videoHeight = $profileOption["videoHeight"];
            $transcodingProfileOption->videoBitrate = $profileOption["videoBitrate"];
            $transcodingProfileOption->audioBitrate = $profileOption["audioBitrate"];
            $transcodingProfileOption->profile = $profileOption["profile"];
            $transcodingProfileOption->videoCodec = $profileOption["videoCodec"];
            $transcodingProfileOption->audioCodec = $profileOption["audioCodec"];
            $this->entityManager->persist($transcodingProfileOption);

            $res[] = "########### {$profileOption["title"]} ########### Profile Added ##########";
        }
        $this->entityManager->flush();


        $profileTitle = "Default";
        $profileExist = $transcodingProfileRepo->findOneBy(["title" => $profileTitle]);

        if (!$profileExist) {

            $transcodingProfile = new TranscodingProfile();
            $transcodingProfile->title = $profileTitle;
            /** @var TranscodingProfileOption $profile */
            $profile = $this->entityManager->getRepository(TranscodingProfileOption::class)->findOneBy(["title" => "SD – 540p"]);
            $transcodingProfile->profiles[] = $profile;
            $transcodingProfile->isDefault = true;
            $this->entityManager->persist($transcodingProfile);
        }

        $this->entityManager->flush();

        return $res;
    }

    public function createDefaultTimeZone()
    {
        $res = [];
        $timeZoneRepo = $this->entityManager->getRepository(TimeZone::class);

        foreach ($this->defaultTimeZone as $timeZoneValue) {
            /** @var TimeZone $timeZoneExist */
            $timeZone = $timeZoneRepo->findOneBy(["title" => $timeZoneValue["title"]]);
            if ($timeZone) {
                $timeZone->position = 1;    
            } else {
                $timeZone = new TimeZone();
            }
            
            $timeZone->title = $timeZoneValue["title"];
            $timeZone->standardTime = $timeZoneValue["standardTime"];
            $timeZone->dayLightSavingTime = $timeZoneValue["dayLightSavingTime"];
            $timeZone->phpTimeZone = $timeZoneValue["phpTimeZone"];
            $timeZone->phpTimeZoneDLS = $timeZoneValue["phpTimeZoneDLS"];
            
            $this->entityManager->persist($timeZone);
            $res[] = "########### {$timeZoneValue["title"]} ########### TimeZone Added ##########";
        }
        $this->entityManager->flush();
        return $res;
    }

    public function createAppSettings()
    {
        $res = [];
        $appSettingRepo = $this->entityManager->getRepository(AppSetting::class);

        foreach ($this->defaultAppParams as $appParam) {
            /** @var AppSetting $appParamExist */
            $appParamExist = $appSettingRepo->findOneBy(["title" => $appParam["title"]]);
            if ($appParamExist) {
                continue;
            }
            $appSetting = new AppSetting();
            $appSetting->setTitle($appParam["title"]);
            $appSetting->paramValue = $appParam["paramValue"];
            $this->entityManager->persist($appSetting);
            $res[] = "########### {$appParam["title"]} ########### App Setting Added ##########";
        }
        $this->entityManager->flush();
        return $res;
    }


    public function createRoles()
    {
        $result = [];
        $roles = UserRoleEnum::getConstants();

        $adminRolesRepo = $this->entityManager->getRepository(AdminRoles::class);

        foreach ($roles as $role) {
            $adminRole = $adminRolesRepo->findOneBy(["code" => $role]);
            if ($adminRole) {
               $result[] = "=========== $role ============= Role already exist ";
                continue;
            }
            $roleTitle = $role;
            $roleTitle = explode("_", $roleTitle);
            $roleTitle = implode(" ", $roleTitle);
            $roleTitle = str_replace("ROLE", "", $roleTitle);
            $roleTitle = str_replace("MNGT", " Manage ", $roleTitle);
            $roleTitle = strtolower($roleTitle);
            // add an Admin Role
            $adminRole = new AdminRoles();
            $adminRole->title = ucwords($roleTitle);
            $adminRole->code = $role;
            $this->entityManager->persist($adminRole);
            $result[] = "########### $role ########### Role Added ##########";
        }

        $this->entityManager->flush();
        return $result;
    }
}