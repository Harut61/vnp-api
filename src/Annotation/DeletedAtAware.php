<?php

namespace App\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class DeletedAtAware
 * @package App\Annotation
 *
 * @Annotation
 * @Target("CLASS")
 */
final class DeletedAtAware
{
    public $deletedAtFieldName;
}
