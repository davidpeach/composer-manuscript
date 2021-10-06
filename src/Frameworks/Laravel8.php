<?php

namespace DavidPeach\Manuscript\Frameworks;

class Laravel8 extends Framework
{
    protected string $name = 'Laravel 8';
    protected string $installCommandSegment = '--prefer-dist laravel/laravel %s "8.*"';
}
