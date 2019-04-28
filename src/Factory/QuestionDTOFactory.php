<?php

declare(strict_types=1);

/*
 * This file is part of the `edUCO` project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Factory;

use App\DTO\AnswerDTO;
use App\DTO\QuestionDTO;
use App\Entity\Answer;
use App\Entity\Question;
use Doctrine\Common\Collections\ArrayCollection;

final class QuestionDTOFactory
{
    public function newQuestionDTO(): QuestionDTO
    {
        $answers = new ArrayCollection();
        $answers->add(new AnswerDTO('', false));
        $answers->add(new AnswerDTO('', false));
        $answers->add(new AnswerDTO('', false));

        return new QuestionDTO('', 0, $answers);
    }

    public function newQuestionDTOFromQuestion(Question $question): QuestionDTO
    {
        $answers = $question->getAnswers()->map(static function (Answer $answer) {
            return new AnswerDTO($answer->getContent(), $answer->getIsCorrect());
        });

        return new QuestionDTO($question->getContent(), $question->getDuration(), $answers);
    }

    public function newQuestionFromDTO(QuestionDTO $questionDTO): Question
    {
        $question = new Question();
        $question->setDuration($questionDTO->getDuration());
        $question->setContent($questionDTO->getContent());

        $questionDTO->getAnswers()->map(static function (AnswerDTO $answerDTO) use ($question): void {
            $answer = new Answer();
            $answer->setContent($answerDTO->getContent());
            $answer->setIsCorrect($answerDTO->isCorrect());

            $question->addAnswer($answer);
        });

        return $question;
    }
}
