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

use Symfony\Component\Validator\Constraints as Assert;

final class AnswerDTO
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @var bool
     */
    private $isCorrect;

    /**
     * AnswerDTO constructor.
     *
     * @param string $content
     * @param bool   $isCorrect
     */
    public function __construct(string $content, bool $isCorrect)
    {
        $this->content = $content;
        $this->isCorrect = $isCorrect;
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
     * @return AnswerDTO
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCorrect(): bool
    {
        return $this->isCorrect;
    }

    /**
     * @param bool $isCorrect
     *
     * @return AnswerDTO
     */
    public function setIsCorrect(bool $isCorrect): self
    {
        $this->isCorrect = $isCorrect;

        return $this;
    }
}
