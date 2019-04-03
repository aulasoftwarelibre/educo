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
use App\Repository\SessionRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class EnableQuestionHandler implements MessageHandlerInterface
{
    /**
     * @var QuestionRepository
     */
    private $questionRepository;
    /**
     * @var SessionRepository
     */
    private $sessionRepository;

    public function __construct(QuestionRepository $questionRepository, SessionRepository $sessionRepository)
    {
        $this->questionRepository = $questionRepository;
        $this->sessionRepository = $sessionRepository;
    }

    public function __invoke(EnableQuestionMessage $enableQuestionMessage): void
    {
        $question = $this->questionRepository->find($enableQuestionMessage->id);
        if (null === $question) {
            throw new \Exception("The question doesn't exist");
        }

        $session = $question->getSession();
        $session->setActiveQuestion($question);

        $this->sessionRepository->save($session);
    }
}