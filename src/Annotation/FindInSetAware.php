<?php

namespace App\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class FindInSetAware
 * @package App\Annotation
 *
 * @Annotation
 * @Target("CLASS")
 */
final class FindInSetAware
{
    public $fieldName;
}
