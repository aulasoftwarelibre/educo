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
use App\Repository\VoteRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RegisterAnswerVoteHandler implements MessageHandlerInterface
{
    /**
     * @var ObjectManager
     */
    private $manager;
    /**
     * @var RequestStack
     */
    private $request;
    /**
     * @var VoteRepository
     */
    private $voteRepository;

    public function __construct(
        ObjectManager $manager,
        RequestStack $request,
        VoteRepository $voteRepository
    ) {
        $this->manager = $manager;
        $this->request = $request;
        $this->voteRepository = $voteRepository;
    }

    public function __invoke(RegisterAnswerVoteRequest $request): void
    {
        $answer = $request->answer;
        $clientIpAddress = $this->request->getCurrentRequest() ? $this->request->getCurrentRequest()->getClientIp() : null;

        if (!$answer->getQuestion()->getActivatedAt()) {
            throw new HttpException(400, 'Question is not open');
        }

        if (!$answer->getQuestion()->getIsAcceptingAnswers()) {
            throw new HttpException(400, 'Question does not accept more votes');
        }

        if ($this->voteRepository->findOneBy([
            'question' => $answer->getQuestion(),
            'clientUniqueId' => $clientIpAddress,
        ])) {
            throw new HttpException(400, 'Already voted');
        }

        $vote = new Vote();
        $vote->setAnswer($answer);
        $vote->setClientUniqueId($clientIpAddress);

        $this->manager->persist($vote);
        $this->manager->flush();
    }
}
