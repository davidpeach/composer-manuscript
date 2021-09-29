<?php

namespace DavidPeach\Manuscript\Frameworks;

class Laravel8 extends Framework
{
    protected $name = 'Laravel 8';
    protected $installCommmandSegment = '--prefer-dist laravel/laravel %s "8.*"';
}
