<?php

namespace App\Enums;

/**
 * Class AppSettingEnum
 * @package App\Enums
 */
class AppSettingEnum extends BasicEnum
{
    const TRANSCODING_PARALLEL = 'Transcoding Parallel';
    const TRANSCODING_PER_DAY = 'Transcoding Per Day';
    const LEDE_LENGTH = "Lede Length";
    const STORY_LENGTH = "Story Length";
    const LEDE_END = "Lede End";
    const STORY_END = "Story End";
}
