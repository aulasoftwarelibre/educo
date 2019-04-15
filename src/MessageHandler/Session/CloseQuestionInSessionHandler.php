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

namespace App\MessageHandler\Session;

use App\Message\Session\CloseQuestionInSessionMessage;
use App\Repository\AnswerRepository;
use App\Repository\SessionRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class CloseQuestionInSessionHandler implements MessageHandlerInterface
{
    /**
     * @var SessionRepository
     */
    private $sessionRepository;
    /**
     * @var AnswerRepository
     */
    private $answerRepository;
    /**
     * @var ObjectManager
     */
    private $manager;

    public function __construct(SessionRepository $sessionRepository, AnswerRepository $answerRepository, ObjectManager $manager)
    {
        $this->sessionRepository = $sessionRepository;
        $this->answerRepository = $answerRepository;
        $this->manager = $manager;
    }

    public function __invoke(CloseQuestionInSessionMessage $message): void
    {
        $sessionId = $message->id;
        $session = $this->sessionRepository->find($sessionId);

        if (!$session) {
            throw new \InvalidArgumentException('Session not found');
        }

        $activeQuestion = $session->getActiveQuestion();

        if (!$activeQuestion) {
            throw new \InvalidArgumentException('No open question in session');
        }

        $session->closeActiveQuestion();
        $this->answerRepository->calculateRates($activeQuestion);

        $this->manager->flush();
    }
}
