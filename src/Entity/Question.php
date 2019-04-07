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

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionRepository")
 * @ApiResource(
 *     mercure=true,
 *     normalizationContext={"groups"={"session"}},
 *     collectionOperations={"get"},
 *     itemOperations={"get"}
 * )
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
     * @Groups("session")
     *
     * @var string
     */
    private $content;

    /**
     * @ORM\Column(type="boolean")
     * @Groups("session")
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
     * @ORM\Column(type="integer")
     * @Groups("session")
     *
     * @var int
     */
    private $duration;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups("session")
     *
     * @var \DateTime|null
     */
    private $activatedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Answer", mappedBy="question", orphanRemoval=true)
     * @ApiSubresource()
     * @Groups("session")
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

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getActivatedAt(): ?\DateTimeInterface
    {
        return $this->activatedAt;
    }

    public function setActivatedAt(?\DateTimeInterface $activatedAt): self
    {
        $this->activatedAt = $activatedAt;

        return $this;
    }
}
