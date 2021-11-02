<?php

namespace DavidPeach\Manuscript;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class Feedback
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function __construct(
        private InputInterface $input,
        private OutputInterface $output,
    ){}

    public function ask(string $question, string $defaultAnswer)
    {
        return (new QuestionHelper)->ask(
            input: $this->input,
            output: $this->output,
            question: new Question(
                $question,
                $defaultAnswer,
            )
        );
    }

    public function choose(string $question, array $choices, int $defaultKey)
    {
        $question = new ChoiceQuestion(
            question: $question,
            choices: $choices,
            default: $defaultKey
        );

        $question->setErrorMessage(errorMessage: 'Unknown option selected: %s.');

        return (new QuestionHelper)->ask(input: $this->input, output: $this->output, question: $question);
    }

    public function print(array $lines)
    {
        $this->output->writeln(messages: $lines);
    }
}
