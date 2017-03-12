<?php

namespace Isaac\Console;

use InvalidArgumentException;
use Isaac\TestCase;
use Symfony\Component\Console\Question\ChoiceQuestion;

class ChoiceValidatorTest extends TestCase
{
    public function testCanPreventMultipleChoicesWhenNotAllowed()
    {
        $this->expectException(InvalidArgumentException::class);

        $question = new ChoiceQuestion('foo', ['foo', 'bar']);
        $validator = new ChoiceValidator($question);
        $validator('0,1');
    }

    public function testCanExtractMultipleChoices()
    {
        $question = new ChoiceQuestion('foo', ['foo', 'bar']);
        $question->setMultiselect(true);

        $validator = new ChoiceValidator($question);
        $answer = $validator('0     ,     1,');

        $this->assertEquals(['0', '1'], $answer);
    }

    public function testCanSanitizeChoices()
    {
        $this->expectException(InvalidArgumentException::class);

        $question = new ChoiceQuestion('foo', ['foo', 'bar']);
        $validator = new ChoiceValidator($question);
        $validator('3');
    }
}