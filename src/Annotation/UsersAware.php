<?php

namespace App\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class UsersAware
 * @package App\Annotation
 *
 * @Annotation
 * @Target("CLASS")
 */
final class UsersAware
{
    public $usersFieldName;
}
