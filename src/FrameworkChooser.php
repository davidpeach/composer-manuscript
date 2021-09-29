<?php

namespace DavidPeach\Manuscript;

use DavidPeach\Manuscript\Frameworks\Framework;
use Symfony\Component\Console\Question\ChoiceQuestion;

class FrameworkChooser
{
    private $frameworks = [
        'laravel6x' => \DavidPeach\Manuscript\Frameworks\Laravel6::class,
        'laravel7x' => \DavidPeach\Manuscript\Frameworks\Laravel7::class,
        'laravel8x' => \DavidPeach\Manuscript\Frameworks\Laravel8::class,
    ];

    public function __construct($input, $output, $helper)
    {
        $this->input = $input;
        $this->output = $output;
        $this->helper = $helper;
    }

    public function choose(): Framework
    {
        $question = new ChoiceQuestion(
            '  Please select your framework',
            array_keys($this->frameworks),
            0
        );
        $question->setErrorMessage('Framework %s is invalid.');

        $chosenFramework = $this->helper->ask($this->input, $this->output, $question);
        $this->output->writeln('<comment>  Installing ' . $chosenFramework . ' as your framework of choice.</comment>');

        return new $this->frameworks[$chosenFramework];
    }
}
