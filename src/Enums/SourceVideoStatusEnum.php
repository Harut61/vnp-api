<?php

namespace App\Enums;

class SourceVideoStatusEnum extends BasicEnum
{
    const UPLOADED = 'UPLOADED';
    const PROCESSING = 'PROCESSING';
    const PROCESSING_FAILED = 'PROCESSING FAILED';
    const READY_FOR_MARKER = 'READY FOR MARKER';
    const PARTIALLY_MARKED_UP = 'PARTIALLY MARKED UP';
    const BEING_MARKED_UP = 'BEING MARKED UP';
    const MARKED_UP_FINISHED = 'MARKED UP FINISHED';
    const ARCHIVED = 'ARCHIVED';
    const FAILED = 'FAILED';
}
