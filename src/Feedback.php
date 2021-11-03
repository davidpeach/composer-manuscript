<?php

namespace DavidPeach\Manuscript;

use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
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
        private InputInterface  $input,
        private OutputInterface $output,
    )
    {
    }

    /**
     * @param string $question
     * @param string $defaultAnswer
     * @return string
     */
    public function ask(string $question, string $defaultAnswer): string
    {
        $answer = (new QuestionHelper)->ask(
            input: $this->input,
            output: $this->output,
            question: new Question(
                question: $question,
                default: $defaultAnswer,
            )
        );

        $this->print(lines: []);

        return $answer;
    }

    /**
     * @param string $question
     * @param array $choices
     * @param int $defaultKey
     * @return string
     */
    public function choose(string $question, array $choices, int $defaultKey): string
    {
        $question = new ChoiceQuestion(
            question: $question,
            choices: $choices,
            default: $defaultKey
        );

        $question->setErrorMessage(errorMessage: 'Unknown option selected: %s.');

        return (new QuestionHelper)->ask(input: $this->input, output: $this->output, question: $question);
    }

    /**
     * @param array $lines
     */
    public function print(array $lines): void
    {
//        $this->output->writeln(messages: '');
        $this->output->writeln(messages: $lines);
        $this->output->writeln(messages: '');
    }
}
