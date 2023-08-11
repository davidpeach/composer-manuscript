<?php

namespace DavidPeach\Manuscript\Frameworks;

class Laravel10 extends Framework
{
    protected string $name = 'Laravel 10';

    protected string $installCommandSegment = '--prefer-dist laravel/laravel %s "10.*"';
}

