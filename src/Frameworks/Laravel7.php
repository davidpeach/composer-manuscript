<?php

namespace DavidPeach\Manuscript\Frameworks;

class Laravel7 extends Framework
{
    protected string $name = 'Laravel 7';

    protected string $installCommandSegment = '--prefer-dist laravel/laravel %s "7.*"';
}
