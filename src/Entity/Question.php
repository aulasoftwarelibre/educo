<?php

declare(strict_types=1);

/*
 * This file is part of the `UCOtrivia` project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionRepository")
 * @ApiResource(mercure=true)
 */
class Question
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=512)
     * @Assert\NotBlank()
     * @Assert\Length(min="10", max="512")
     *
     * @var string
     */
    private $content;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $isAcceptingAnswers;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Session", inversedBy="questions")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid()
     *
     * @var Session
     */
    private $session;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Answer", mappedBy="question", orphanRemoval=true)
     *
     * @var Collection
     */
    private $answers;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
        $this->isAcceptingAnswers = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getIsAcceptingAnswers(): ?bool
    {
        return $this->isAcceptingAnswers;
    }

    public function setIsAcceptingAnswers(bool $isAcceptingAnswers): self
    {
        $this->isAcceptingAnswers = $isAcceptingAnswers;

        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(Session $session): self
    {
        $this->session = $session;

        return $this;
    }

    /**
     * @return Collection|Answer[]
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setQuestion($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->contains($answer)) {
            $this->answers->removeElement($answer);
        }

        return $this;
    }
}
