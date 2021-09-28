<?php

namespace DavidPeach\Manuscript;

use Illuminate\Support\Str;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class FreshPackage extends Package
{
    public function getPath(): string
    {
        return $this->directory . '/' . $this->folderName;
    }

    public function getName(): string
    {
        return $this->data['name'];
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getData(): void
    {
        $this->data['name'] = $this->determineName();
        $this->output->writeln('  <comment>' . $this->data['name'] . "</comment>\n");

        $this->data['description'] = $this->determineDescription();
        $this->output->writeln('  <comment>' . $this->data['description'] . "</comment>\n");

        $authorName = $this->determineAuthorName();
        $this->output->writeln('  <comment>' . $authorName . "</comment>\n");

        $authorEmail = $this->determineAuthorEmail();
        $this->output->writeln('  <comment>' . $authorEmail . "</comment>\n");

        $this->data['author'] = $authorName . ' <' . $authorEmail . '>';

        $this->data['stability'] = $this->determineStability();
        $this->output->writeln('  <comment>' . $this->data['stability'] . "</comment>\n");

        $this->data['license'] = $this->determineLicense();
        $this->output->writeln('  <comment>' . $this->data['license'] . "</comment>\n");

        $this->namespace = $this->determineNameSpace();
        $this->folderName = $this->determineFolderName();
    }

    public function scaffold(): void
    {
        $fullPath = $this->getPath();

        if (file_exists($fullPath)) {
            throw new \Exception($fullPath . ' already exists', 1);
        }

        mkdir($fullPath);

        $composerBuildCommand = [
            'composer init',
        ];

        foreach ($this->data as $key => $value) {
            if (!empty($this->data[$key])) {
                $composerBuildCommand[] = '--' . $key . '="' . $value . '"';
            }
        }

        $composerBuildCommand[] = '--autoload="src/"';

        $commands = [
            'cd ' . $fullPath,
            implode(' ', $composerBuildCommand),
            'cd ../',
        ];

        $process = Process::fromShellCommandline(implode(' && ', $commands));
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        file_put_contents(
            $fullPath . '/src/Quote.php',
            str_replace(
                '{#NAMESPACE#}',
                trim($this->getNamespace(), '\\'),
                file_get_contents(__DIR__ . '/../stubs/Quote.stub')
            )
        );
    }

    private function determineName(): string
    {
        $question = new Question(' <question> Please enter the name of your package [wow/such-package] </question> : ', 'wow/such-package');

        return $this->helper->ask($this->input, $this->output, $question);
    }

    private function determineDescription(): string
    {
        $question = new Question(' <question> Please enter the description of your package </question> : ', '');

        return $this->helper->ask($this->input, $this->output, $question) ?? '';
    }

    private function determineAuthorName(): string
    {
        $question = new Question(' <question> Please enter the author name of your package</question> : ', 'name here');

        return $this->helper->ask($this->input, $this->output, $question);
    }

    private function determineAuthorEmail(): string
    {
        $question = new Question(' <question> Please enter the author email of your package</question> : ', 'email@example.com');

        return $this->helper->ask($this->input, $this->output, $question);
    }

    private function determineStability(): string
    {
        $question = new ChoiceQuestion(
            ' <question> Please select your minimum stability [stable] </question> : ',
            ['dev', 'alpha', 'beta', 'RC', 'stable'],
            4
        );
        $question->setErrorMessage('Minimum Stability %s is invalid.');

        return $this->helper->ask($this->input, $this->output, $question);
    }

    private function determineLicense(): string
    {
        $question = new Question(' <question> Please enter the license for your package [MIT] </question> : ', 'MIT');

        return $this->helper->ask($this->input, $this->output, $question);
    }

    private function determineFolderName(): string
    {
        $parts = explode('/', $this->data['name']);
        return end($parts);
    }

    private function determineNameSpace()
    {
        $parts = explode('/', $this->data['name']);
        $firstPart = Str::studly($parts[0]);
        $secondPart = Str::studly($parts[1]);

        return implode('\\', [$firstPart, $secondPart]) . '\\';
    }
}
