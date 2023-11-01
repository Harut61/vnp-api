<?php

namespace App\Enums;

class StoryQAStatusEnum extends BasicEnum
{
    const PENDING_QA = 'PendingQA';
    const QA_ISSUE_FLAGGED = 'QAIssueFlagged';
    const QA_REVIEW_PASSED = 'QAReviewPassed';
    const ON_QA_REVIEW = 'OnQAReview';
    const TAGGING_COMPLETED = 'TaggingCompleted';
}
