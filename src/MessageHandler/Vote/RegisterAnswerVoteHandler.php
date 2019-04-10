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

namespace App\MessageHandler\Vote;

use App\Entity\Vote;
use App\Message\Vote\RegisterAnswerVoteRequest;
use App\Repository\AnswerRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RegisterAnswerVoteHandler implements MessageHandlerInterface
{
    /**
     * @var AnswerRepository
     */
    private $answerRepository;
    /**
     * @var ObjectManager
     */
    private $manager;

    public function __construct(
        AnswerRepository $answerRepository,
        ObjectManager $manager
    ) {
        $this->answerRepository = $answerRepository;
        $this->manager = $manager;
    }

    public function __invoke(RegisterAnswerVoteRequest $request): void
    {
        $answer = $request->answer;

        if (!$answer->getQuestion()->getActivatedAt()) {
            throw new HttpException(400, 'Question is not open');
        }

        if (!$answer->getQuestion()->getIsAcceptingAnswers()) {
            throw new HttpException(400, 'Question does not accept more votes');
        }

        $vote = new Vote();
        $vote->setAnswer($answer);

        $this->manager->persist($vote);
        $this->manager->flush();
    }
}
