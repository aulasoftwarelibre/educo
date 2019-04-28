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

namespace App\DTO;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class QuestionDTO
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0)
     */
    private $duration;

    /**
     * @var Collection|AnswerDTO[]
     */
    private $answers;

    /**
     * QuestionDTO constructor.
     *
     * @param string                 $content
     * @param int                    $duration
     * @param AnswerDTO[]|Collection $answers
     */
    public function __construct(string $content, int $duration, Collection $answers)
    {
        $this->content = $content;
        $this->duration = $duration;
        $this->answers = $answers;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return QuestionDTO
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return int
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     *
     * @return QuestionDTO
     */
    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return AnswerDTO[]|ArrayCollection
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    /**
     * @param AnswerDTO[]|ArrayCollection $answers
     *
     * @return QuestionDTO
     */
    public function setAnswers($answers): self
    {
        $this->answers = $answers;

        return $this;
    }

    /**
     * @Assert\Callback()
     */
    public static function validate(self $object, ExecutionContextInterface $context, $payload): void
    {
        if (1 !== $object->getAnswers()->filter(static function (AnswerDTO $answerDTO) {
            return $answerDTO->isCorrect();
        })->count()) {
            $context->buildViolation('Solo una respuesta correcta')
                ->atPath('answers')
                ->addViolation();
        }
    }
}
