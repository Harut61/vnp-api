<?php

namespace App\Services\Aws;

use App\Entity\TranscodingProfile;
use App\Entity\TranscodingProfileOption;
use App\Entity\Vod;
use App\Util\AwsS3Util;
use Aws\Credentials\Credentials;
use Aws\Exception\AwsException;
use Aws\MediaConvert\MediaConvertClient;
use Doctrine\ORM\EntityManagerInterface;

class AwsMediaConvertService
{
    /** @var  EntityManagerInterface $entityManager */
    protected $entityManager;

    /** @var  AwsS3Util $awsS3Util */
    protected $awsS3Util;

    /** @var  MediaConvertClient $client */
    protected $client;

    /**
     * AwsMediaConvertService constructor.
     * @param EntityManagerInterface $entityManager
     * @param AwsS3Util $awsS3Util
     */
    public function __construct(EntityManagerInterface $entityManager, AwsS3Util $awsS3Util)
    {
        $this->entityManager = $entityManager;
        $this->awsS3Util = $awsS3Util;
    }

    /**
     * @param $accessKey
     * @param $secretKey
     * @param string $region
     * @param string $version
     * @param $endPoint
     * @return MediaConvertClient
     */
    public function setClient($accessKey, $secretKey, $region = 'us-east-1', $version = 'latest', $endPoint)
    {

        $credentials = new Credentials($accessKey, $secretKey);
        $this->client = new MediaConvertClient([
            'region' => $region,
            'version' => "latest",
            'credentials' => $credentials,
            "endpoint" => $endPoint
        ]);

        return $this->client;
    }

    /**
     * @param $jobName
     * @param $inputPath
     * @param bool $extractCaption
     * @param bool $clipping
     * @param Vod $vod
     * @return \Aws\Result
     */
    public function createJob($jobName ,$inputPath, $extractCaption = true, $clipping = true, Vod $vod)
    {
        $mediaConvertClient = $this->setClient(
            getenv("AWS_MEDIA_CONVERTER_ACCESS_KEY"),
            getenv("AWS_MEDIA_CONVERTER_SECRET_KEY"),
            getenv("AWS_MEDIA_CONVERTER_REGION"),
            getenv("AWS_MEDIA_CONVERTER_VERSION"),
            getenv("AWS_MEDIA_CONVERTER_ENDPOINT")
        );

        $startTime = $endTime = false;
        if($clipping){
            $startTime = str_replace(".", ":" , $vod->story->storyStart);
            $endTime = str_replace( ".", ":", $vod->story->storyEnd);
        }

        $settings = [
            "AdAvailOffset" => 0,
            "TimecodeConfig" => [
                "Source" => "ZEROBASED"
            ],
            "Inputs" => $this->getInput($inputPath, $clipping, $startTime, $endTime),
            "OutputGroups" => $this->getOutPutGroups($vod->getId(), $extractCaption)
        ];


        try {
            $jobParams = [
                "Role" => getenv("AWS_MEDIA_CONVERTER_IAM"),
                "Name" => $jobName,
                "Settings" => $settings,
                "StatusUpdateInterval" => "SECONDS_10",
                "UserMetadata" => [
                    "vod_id" => $vod->getId(),
                    "app_env" => getenv( "IVN_ENV"),
                    "app_name" => getenv("APP_NAME")
                ]
            ];

            return $mediaConvertClient->createJob($jobParams);
        } catch (AwsException $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function getOutPutGroups($vodId, $extractCaption = false)
    {
        /** @var TranscodingProfile $transcodingProfile */
        $transcodingProfile = $this->entityManager->getRepository(TranscodingProfile::class)->findOneBy(["isDefault" => true]);

        $outputs = [];
        $outputsHls = [];

        if($extractCaption) {
            $outputs[] = $this->getCCOutput($vodId);
            $outputs[] = $this->getAudioOutput($vodId);

        }
        $newProfileOption = new TranscodingProfileOption();
        $newProfileOption->videoBitrate = 600000;
        $newProfileOption->videoWidth = 426;
        $newProfileOption->videoHeight = 240;
        $newProfileOption->audioBitrate = 96000;
        $newProfileOption->fps = 30;
        $newProfileOption->title = "hls-m";
        $newProfileOption->setSlug("hls-m");
        $outputsHls[] = $this->getHlsOutput($newProfileOption, $vodId);

        /** @var TranscodingProfileOption $profileOption */
        foreach ($transcodingProfile->profiles as $profileOption) {
            $outputs[] = $this->getOutput($profileOption, $vodId);
        }

        $outputBucketName = $this->awsS3Util->getBucketName(getenv("AWS_S3_TRANSCODING_OUTPUT_BUCKET"));

        if($extractCaption){
            $outputGroups =  $this->getHlsOutPutGroup($outputsHls, $outputBucketName);
            $outputGroupsMp4 = $this->getMp4OutputGroup($outputs, $outputBucketName);
            $outputGroups = array_merge($outputGroups, $outputGroupsMp4);

        } else {
            $outputGroups = $this->getHlsOutPutGroup($outputsHls, $outputBucketName, $vodId);
        }
        return $outputGroups;
    }

    public function getOutput(TranscodingProfileOption $profileOption, $vodId)
    {
        return [
            'ContainerSettings' => [
                'Container' => 'MP4',
                'Mp4Settings' => [
                    'CslgAtom' => 'INCLUDE',
                    'CttsVersion' => 0,
                    'FreeSpaceBox' => 'EXCLUDE',
                    'MoovPlacement' => 'PROGRESSIVE_DOWNLOAD',
                ],
            ],
            'VideoDescription' => [
                'Width' => $profileOption->videoWidth,
                'ScalingBehavior' => 'DEFAULT',
                'Height' => $profileOption->videoHeight,
                'TimecodeInsertion' => 'DISABLED',
                'AntiAlias' => 'ENABLED',
                'Sharpness' => 50,
                'CodecSettings' => [
                    'Codec' => $profileOption->videoCodec,
                    'H264Settings' => [
                        'InterlaceMode' => 'PROGRESSIVE',
                        'NumberReferenceFrames' => 3,
                        'Syntax' => 'DEFAULT',
                        'Softness' => 0,
                        'GopClosedCadence' => 1,
                        'GopSize' => 90,
                        'Slices' => 1,
                        'GopBReference' => 'DISABLED',
                        'SlowPal' => 'DISABLED',
                        'SpatialAdaptiveQuantization' => 'ENABLED',
                        'TemporalAdaptiveQuantization' => 'ENABLED',
                        'FlickerAdaptiveQuantization' => 'DISABLED',
                        'EntropyEncoding' => 'CABAC',
                        'Bitrate' => $profileOption->videoBitrate,
                        'FramerateControl' => 'INITIALIZE_FROM_SOURCE',
                        'RateControlMode' => 'CBR',
                        'CodecProfile' => $profileOption->profile,
                        'Telecine' => 'NONE',
                        'MinIInterval' => 0,
                        'AdaptiveQuantization' => 'HIGH',
                        'CodecLevel' => 'AUTO',
                        'FieldEncoding' => 'PAFF',
                        'SceneChangeDetect' => 'ENABLED',
                        'QualityTuningLevel' => 'SINGLE_PASS',
                        'FramerateConversionAlgorithm' => 'DUPLICATE_DROP',
                        'UnregisteredSeiTimecode' => 'DISABLED',
                        'GopSizeUnits' => 'FRAMES',
                        'ParControl' => 'INITIALIZE_FROM_SOURCE',
                        'NumberBFramesBetweenReferenceFrames' => 2,
                        'RepeatPps' => 'DISABLED',
                        'DynamicSubGop' => 'STATIC',
                        'FramerateNumerator' => $profileOption->fps,
                        'FramerateDenominator' => 1,
                        'ParNumerator' => 1,
                        'ParDenominator' => 1,
                    ],
                ],
                'AfdSignaling' => 'NONE',
                'DropFrameTimecode' => 'ENABLED',
                'RespondToAfd' => 'NONE',
                'ColorMetadata' => 'INSERT',
            ],
            'AudioDescriptions' => [
                [
                    'AudioTypeControl' => 'FOLLOW_INPUT',
                    'CodecSettings' => [
                        'Codec' => $profileOption->audioCodec,
                        'AacSettings' => [
                            'AudioDescriptionBroadcasterMix' => 'NORMAL',
                            'Bitrate' => $profileOption->audioBitrate,
                            'RateControlMode' => 'CBR',
                            'CodecProfile' => 'LC',
                            'CodingMode' => 'CODING_MODE_2_0',
                            'RawFormat' => 'NONE',
                            'SampleRate' => 48000,
                            'Specification' => 'MPEG4',
                        ],
                    ],
                    'LanguageCodeControl' => 'FOLLOW_INPUT',
                ],
            ],
            'NameModifier' => '-'.$profileOption->getSlug()."-$vodId"
        ];
    }

    public function getHlsOutput(TranscodingProfileOption $profileOption, $vodId)
    {
        return [
            'ContainerSettings' => [
                'Container' => 'M3U8',
                'M3u8Settings' => [
                    'AudioFramesPerPes' => 4,
                    'PcrControl' => 'PCR_EVERY_PES_PACKET',
                    'PmtPid' => 480,
                    'PrivateMetadataPid' => 503,
                    'ProgramNumber' => 1,
                    'PatInterval' => 0,
                    'PmtInterval' => 0,
                    'Scte35Source' => 'NONE',
                    'NielsenId3' => 'NONE',
                    'TimedMetadata' => 'NONE',
                    'VideoPid' => 481,
                    'AudioPids' =>
                        [
                            0 => 482,
                            1 => 483,
                            2 => 484,
                            3 => 485,
                            4 => 486,
                            5 => 487,
                            6 => 488,
                            7 => 489,
                            8 => 490,
                            9 => 491,
                            10 => 492,
                        ],
                    'AudioDuration' => 'DEFAULT_CODEC_DURATION',
                ],
            ],
            'VideoDescription' => [
                'Width' => $profileOption->videoWidth,
                'ScalingBehavior' => 'DEFAULT',
                'Height' => $profileOption->videoHeight,
                'TimecodeInsertion' => 'DISABLED',
                'AntiAlias' => 'ENABLED',
                'Sharpness' => 50,
                'CodecSettings' =>
                    [
                        'Codec' => 'H_264',
                        'H264Settings' =>
                            [
                                'InterlaceMode' => 'PROGRESSIVE',
                                'ScanTypeConversionMode' => 'INTERLACED',
                                'NumberReferenceFrames' => 3,
                                'Syntax' => 'DEFAULT',
                                'Softness' => 0,
                                'GopClosedCadence' => 1,
                                'GopSize' => 90,
                                'Slices' => 1,
                                'GopBReference' => 'DISABLED',
                                'HrdBufferSize' => 3750,
                                'MaxBitrate' => $profileOption->videoBitrate,
                                'SlowPal' => 'DISABLED',
                                'EntropyEncoding' => 'CABAC',
//                                'Bitrate' => $profileOption->videoBitrate,
                                'FramerateControl' => 'INITIALIZE_FROM_SOURCE',
                                'RateControlMode' => 'QVBR',
                                'QvbrSettings' => [
                                    'QvbrQualityLevel' => 7,
                                    'QvbrQualityLevelFineTune' => 0.33,
                                ],
                                'CodecProfile' => 'MAIN',
                                'Telecine' => 'NONE',
                                'MinIInterval' => 0,
                                'AdaptiveQuantization' => 'AUTO',
                                'CodecLevel' => 'AUTO',
                                'FieldEncoding' => 'PAFF',
                                'SceneChangeDetect' => 'ENABLED',
                                'QualityTuningLevel' => 'SINGLE_PASS',
                                'FramerateConversionAlgorithm' => 'DUPLICATE_DROP',
                                'UnregisteredSeiTimecode' => 'DISABLED',
                                'GopSizeUnits' => 'FRAMES',
                                'ParControl' => 'INITIALIZE_FROM_SOURCE',
                                'NumberBFramesBetweenReferenceFrames' => 2,
                                'RepeatPps' => 'DISABLED',
                                'DynamicSubGop' => 'STATIC',
                            ],
                    ],
                'AfdSignaling' => 'NONE',
                'DropFrameTimecode' => 'ENABLED',
                'RespondToAfd' => 'NONE',
                'ColorMetadata' => 'INSERT',
            ],
            'AudioDescriptions' => [
                [
                    'AudioTypeControl' => 'FOLLOW_INPUT',
                    'CodecSettings' =>
                        [
                            'Codec' => 'AAC',
                            'AacSettings' =>
                                [
                                    'AudioDescriptionBroadcasterMix' => 'NORMAL',
                                    'Bitrate' => $profileOption->audioBitrate,
                                    'RateControlMode' => 'CBR',
                                    'CodecProfile' => 'LC',
                                    'CodingMode' => 'CODING_MODE_2_0',
                                    'RawFormat' => 'NONE',
                                    'SampleRate' => 48000,
                                    'Specification' => 'MPEG4'
                                ],
                        ],
                    'LanguageCodeControl' => 'FOLLOW_INPUT',
                ],
            ],
            "OutputSettings" => [
                'HlsSettings' =>
                    [
                        'AudioGroupId' => 'program_audio',
                        'AudioOnlyContainer' => 'AUTOMATIC',
                        'IFrameOnlyManifest' => 'EXCLUDE',
                    ],
            ],
            'NameModifier' => '-'.$profileOption->getSlug()."-$vodId"
        ];
    }

    public function getInput($inputPath, $clipping = false, $startTime = false, $endTime = false)
    {
        $inputParams =  [
            'AudioSelectors' =>
                [
                    'Audio Selector 1' =>
                        [
                            'Offset' => 0,
                            'DefaultSelection' => 'DEFAULT',
                            'ProgramSelection' => 1,
                        ],
                ],
            'VideoSelector' =>
                [
                    'ColorSpace' => 'REC_709',
                    'Rotate' => 'DEGREE_0',
                    'AlphaBehavior' => 'DISCARD',
                    "ColorSpaceUsage"=> "FORCE"
                ],
            'FilterEnable' => 'AUTO',
            'PsiControl' => 'USE_PSI',
            'FilterStrength' => 0,
            'DeblockFilter' => 'DISABLED',
            'DenoiseFilter' => 'DISABLED',
            'InputScanType' => 'AUTO',
            'TimecodeSource' => 'ZEROBASED',
            'FileInput' => $inputPath,
            'CaptionSelectors' =>
                [
                    'Captions Selector 1' =>
                        [
                            'SourceSettings' =>
                                [
                                    'SourceType' => 'EMBEDDED',
                                    'EmbeddedSourceSettings' =>
                                        [
                                            'Source608ChannelNumber' => 1,
                                            'Source608TrackNumber' => 1,
                                            'Convert608To708' => 'DISABLED',
                                            'TerminateCaptions' => 'END_OF_INPUT',
                                        ]
                                ]
                        ]
                ]
        ];

        if($clipping)
        {
            $inputParams["InputClippings"] =  [
                [
                    "StartTimecode" => $startTime,
                    "EndTimecode" => $endTime,
                ]
            ];

        }

        return [$inputParams];
    }


    public function getCCOutput($vodId){
        return [
            'ContainerSettings' =>
                [
                    'Container' => 'MP4',
                    'Mp4Settings' =>
                        [
                            'CslgAtom' => 'INCLUDE',
                            'CttsVersion' => 0,
                            'FreeSpaceBox' => 'EXCLUDE',
                            'MoovPlacement' => 'PROGRESSIVE_DOWNLOAD',
                        ],
                ],
            'NameModifier' => "-close-caption-$vodId",
            'CaptionDescriptions' =>
                [
                    [
                        'CaptionSelectorName' => 'Captions Selector 1',
                        'DestinationSettings' =>
                            [
                                'DestinationType' => 'WEBVTT',
                            ],
                        'LanguageCode' => 'ENG',
                    ]
                ]
        ];
    }


    public function getAudioOutput($vodId){
        return [
            'ContainerSettings' =>
                [
                    'Container' => 'MP4',
                    'Mp4Settings' =>
                        [
                            'CslgAtom' => 'INCLUDE',
                            'CttsVersion' => 0,
                            'FreeSpaceBox' => 'EXCLUDE',
                            'MoovPlacement' => 'PROGRESSIVE_DOWNLOAD',
                            'AudioDuration' => 'DEFAULT_CODEC_DURATION',
                        ],
                ],
            'AudioDescriptions' =>
                [
                    [
                        'AudioTypeControl' => 'FOLLOW_INPUT',
                        'AudioSourceName' => 'Audio Selector 1',
                        'CodecSettings' =>
                            [
                                'Codec' => 'AAC',
                                'AacSettings' =>
                                    [
                                        'AudioDescriptionBroadcasterMix' => 'NORMAL',
                                        'Bitrate' => 96000,
                                        'RateControlMode' => 'CBR',
                                        'CodecProfile' => 'LC',
                                        'CodingMode' => 'CODING_MODE_2_0',
                                        'RawFormat' => 'NONE',
                                        'SampleRate' => 48000,
                                        'Specification' => 'MPEG4',
                                    ],
                            ],
                        'LanguageCodeControl' => 'FOLLOW_INPUT',
                    ],
                ],
            'NameModifier' => "-audio-$vodId"
        ];
    }

    public function getHlsOutPutGroup($outputs, $outputBucketName, $vodId = null)
    {
        return [
            [
                "CustomName" => "Hls 240",
                "Name" => "File Group",
                "Outputs" => $outputs,
                "OutputGroupSettings" => [
                    'Type' => 'HLS_GROUP_SETTINGS',
                    "HlsGroupSettings" => [
                        'ManifestDurationFormat' => 'INTEGER',
                        'SegmentLength' => 5,
                        'TimedMetadataId3Period' => 10,
                        'CaptionLanguageSetting' => 'OMIT',
                        'Destination' => ($vodId) ? "s3://$outputBucketName/$vodId/hls/"  : 's3://'.$outputBucketName.'/$fn$/hls/' ,
                        'TimedMetadataId3Frame' => 'PRIV',
                        'CodecSpecification' => 'RFC_4281',
                        'OutputSelection' => 'MANIFESTS_AND_SEGMENTS',
                        'ProgramDateTimePeriod' => 600,
                        'MinSegmentLength' => 0,
                        'MinFinalSegmentLength' => 0,
                        'DirectoryStructure' => 'SINGLE_DIRECTORY',
                        'ProgramDateTime' => 'EXCLUDE',
                        'SegmentControl' => 'SEGMENTED_FILES',
                        'ManifestCompression' => 'NONE',
                        'ClientCache' => 'ENABLED',
                        'AudioOnlyHeader' => 'INCLUDE',
                        'StreamInfResolution' => 'INCLUDE',
                    ]
                ],

            ]
        ];
    }

    public function getMp4OutputGroup($outputs, $outputBucketName)
    {
        return [
            [
                "CustomName" => "MP4 OUTPUT",
                "Name" => "File Group",
                "Outputs" => $outputs,
                "OutputGroupSettings" => [
                    'Type' => 'FILE_GROUP_SETTINGS',
                    'FileGroupSettings' =>
                        [
                            'Destination' => 's3://'.$outputBucketName.'/$fn$/',
                        ]
                ]
            ]
        ];
    }

}
