<?php

namespace DavidPeach\Manuscript;

use DavidPeach\Manuscript\Frameworks\Framework;
use DavidPeach\Manuscript\Frameworks\Laravel6;
use DavidPeach\Manuscript\Frameworks\Laravel7;
use DavidPeach\Manuscript\Frameworks\Laravel8;

class FrameworkChooser
{
    private array $frameworks = [
        'laravel6x' => Laravel6::class,
        'laravel7x' => Laravel7::class,
        'laravel8x' => Laravel8::class,
    ];

    public function __construct(
        private Feedback $feedback
    ){}

    public function choose(): Framework
    {
        $chosenFramework = $this->feedback->choose(
            question: 'Please select your framework',
            choices: array_keys($this->frameworks),
            defaultKey: 0
        );

        $this->feedback->print(
            lines: ['<comment>  Installing ' . $chosenFramework . ' as your framework of choice.</comment>']
        );

        return new $this->frameworks[$chosenFramework];
    }
}
