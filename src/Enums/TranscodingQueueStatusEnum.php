<?php

namespace App\Enums;

class TranscodingQueueStatusEnum extends BasicEnum
{
    const INITIALIZED = 'INITIALIZED';
    const ERROR = 'ERROR';
    const SUBMITTED = 'SUBMITTED';
    const PROGRESSING = 'PROGRESSING';
    const COMPLETE = 'COMPLETE';
    const CANCELED = 'CANCELED';
}
