<?php

namespace DavidPeach\Manuscript;

use DavidPeach\Manuscript\Frameworks\Framework;
use DavidPeach\Manuscript\Frameworks\Laravel6;
use DavidPeach\Manuscript\Frameworks\Laravel7;
use DavidPeach\Manuscript\Frameworks\Laravel8;
use Symfony\Component\Console\Style\StyleInterface;

class FrameworkChooser
{
    private ?StyleInterface $io = null;

    private array $frameworks = [
        'laravel6x' => Laravel6::class,
        'laravel7x' => Laravel7::class,
        'laravel8x' => Laravel8::class,
    ];

    public function setIO(StyleInterface $io): self
    {
        $this->io = $io;

        return $this;
    }

    /**
     * @return Framework
     */
    public function choose(): Framework
    {
        $chosenFramework = $this->io->choice(
            question: 'Please select your framework',
            choices: array_keys($this->frameworks),
            default: 0
        );

        $this->io->info(
            message: ['Installing ' . $chosenFramework . ' as your framework of choice.']
        );

        return new $this->frameworks[$chosenFramework];
    }
}
