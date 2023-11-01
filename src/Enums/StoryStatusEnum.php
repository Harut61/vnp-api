<?php

namespace App\Enums;

class StoryStatusEnum extends BasicEnum
{
    const QUEUED = 'queued';
    const GENERATING = 'generating';
    const STORY_GENERATION_FAILED = 'story_generation_failed';
    const GENERATED = 'generated';
    const ARCHIVED = 'archived';
}
