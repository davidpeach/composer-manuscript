<?php

namespace DavidPeach\Manuscript;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class QuestionAsker
{

    private string $question;

    private string $defaultAnswer;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $helper
     */
    public function __construct(
        private InputInterface $input,
        private OutputInterface $output,
        private QuestionHelper $helper
    )
    {}

    public function question(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function defaultAnswer(string $defaultAnswer): self
    {
        $this->defaultAnswer = $defaultAnswer;

        return $this;
    }

    public function ask()
    {
        return $this->helper->ask(
            $this->input,
            $this->output,
            new Question(
                $this->question,
                $this->defaultAnswer,
            )
        );
    }
}
