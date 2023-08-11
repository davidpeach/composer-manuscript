<?php

namespace DavidPeach\Manuscript\Frameworks;

use Symfony\Component\Console\Style\StyleInterface;

class FrameworkChooser
{
    private ?StyleInterface $io = null;

    private array $frameworks = [
        'laravel10x' => Laravel10::class,
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
