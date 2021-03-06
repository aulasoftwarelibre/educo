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
use App\Controller\Api\VoteAnswerController;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnswerRepository")
 * @ApiResource(
 *     mercure=true,
 *     normalizationContext={"groups"={"session"}},
 *     collectionOperations={},
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"session"}},
 *          },
 *          "put_vote"={
 *              "method"="PUT",
 *              "status"=204,
 *              "path"="/answers/{id}/vote",
 *              "controller"=VoteAnswerController::class,
 *              "denormalization_context"={"groups"={"vote"}},
 *          }
 *     },
 * )
 */
class Answer
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
     * @Groups({"session", "can_retrieve_right_answer"})
     *
     * @var string
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Question", inversedBy="answers", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid()
     *
     * @var Question
     */
    private $question;

    /**
     * @ORM\Column(type="boolean")
     * @Groups("can_retrieve_right_answer")
     *
     * @var bool
     */
    private $isCorrect;

    /**
     * @ORM\Column(type="float", nullable=true, precision=3, scale=1)
     * @Groups("can_retrieve_right_answer")
     *
     * @var float
     */
    private $rate;

    public function __construct()
    {
        $this->isCorrect = false;
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

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(Question $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getIsCorrect(): ?bool
    {
        return $this->isCorrect;
    }

    public function setIsCorrect(bool $isCorrect): self
    {
        $this->isCorrect = $isCorrect;

        return $this;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(?float $rate): self
    {
        $this->rate = $rate;

        return $this;
    }
}
