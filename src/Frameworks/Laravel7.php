<?php

namespace Davidpeach\Manuscript\Frameworks;

class Laravel7 extends Framework
{
    protected $name = 'Laravel 7';
    protected $installCommmandSegment = '--prefer-dist laravel/laravel %s "7.*"';
}
