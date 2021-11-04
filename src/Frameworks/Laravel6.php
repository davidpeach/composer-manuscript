<?php

namespace DavidPeach\Manuscript\Frameworks;

class Laravel6 extends Framework
{
    protected string $name = 'Laravel 6';

    protected string $installCommandSegment = '--prefer-dist laravel/laravel %s "6.*"';
}
