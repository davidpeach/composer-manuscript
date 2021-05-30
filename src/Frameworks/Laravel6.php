<?php

namespace Davidpeach\Manuscript\Frameworks;

class Laravel6 extends Framework
{
    protected $name = 'Laravel 6';
    protected $installCommmandSegment = '--prefer-dist laravel/laravel %s "6.*"';
}
