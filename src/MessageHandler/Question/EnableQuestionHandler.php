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

namespace App\MessageHandler\Question;

use App\Message\Question\EnableQuestionMessage;
use App\Repository\QuestionRepository;
use App\Repository\VoteRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class EnableQuestionHandler implements MessageHandlerInterface
{
    /**
     * @var QuestionRepository
     */
    private $questionRepository;
    /**
     * @var ObjectManager
     */
    private $manager;
    /**
     * @var VoteRepository
     */
    private $voteRepository;

    public function __construct(
        QuestionRepository $questionRepository,
        VoteRepository $voteRepository,
        ObjectManager $manager
    ) {
        $this->questionRepository = $questionRepository;
        $this->manager = $manager;
        $this->voteRepository = $voteRepository;
    }

    public function __invoke(EnableQuestionMessage $enableQuestionMessage): void
    {
        $question = $this->questionRepository->find($enableQuestionMessage->getId());
        if (null === $question) {
            throw new \Exception("The question doesn't exist");
        }

        $session = $question->getSession();
        $session->setActiveQuestion($question);

        $this->voteRepository->cleanVotes($question);

        $this->manager->flush();
    }
}
