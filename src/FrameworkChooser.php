<?php

namespace Davidpeach\Manuscript;

use Davidpeach\Manuscript\Frameworks\Framework;
use Symfony\Component\Console\Question\ChoiceQuestion;

class FrameworkChooser
{
    private $frameworks = [
        'laravel 6' => \Davidpeach\Manuscript\Frameworks\Laravel6::class,
        'laravel 7' => \Davidpeach\Manuscript\Frameworks\Laravel7::class,
        'laravel 8' => \Davidpeach\Manuscript\Frameworks\Laravel8::class,
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
