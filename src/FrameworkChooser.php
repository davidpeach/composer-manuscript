<?php

namespace DavidPeach\Manuscript;

use DavidPeach\Manuscript\Frameworks\Framework;
use DavidPeach\Manuscript\Frameworks\Laravel6;
use DavidPeach\Manuscript\Frameworks\Laravel7;
use DavidPeach\Manuscript\Frameworks\Laravel8;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class FrameworkChooser
{
    private array $frameworks = [
        'laravel6x' => Laravel6::class,
        'laravel7x' => Laravel7::class,
        'laravel8x' => Laravel8::class,
    ];

    public function __construct(private InputInterface $input, private OutputInterface $output, private QuestionHelper
    $helper)
    {
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
