<?php

namespace Isaac\Console;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * A more lenient choice validator that allows no answer to be given.
 */
class ChoiceValidator
{
    /**
     * @var ChoiceQuestion
     */
    protected $question;

    /**
     * @param ChoiceQuestion $question
     */
    public function __construct(ChoiceQuestion $question)
    {
        $this->question = $question;
    }

    /**
     * @param string|null $answer
     *
     * @return array
     */
    public function __invoke(string $answer = null): array
    {
        // Explode into an array
        $answer = str_replace(' ', '', (string) $answer);
        $answer = explode(',', $answer);
        $answer = array_filter($answer, function ($choice) {
            return $choice !== '';
        });

        // Validate number of answers
        if (!$this->question->isMultiselect() && count($answer) > 1) {
            throw new InvalidArgumentException('Only one answer is allowed here');
        }

        // Validate choices
        $choices = $this->question->getChoices();
        foreach ($answer as $choice) {
            if (!array_key_exists($choice, $choices)) {
                throw new InvalidArgumentException(sprintf('Invalid choice "%s"', $choice));
            }
        }

        return $answer;
    }
}
