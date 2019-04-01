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

use App\Message\Session\DisableQuestionMessage;
use App\Repository\SessionRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DisableQuestionHandler implements MessageHandlerInterface
{
    /**
     * @var SessionRepository
     */
    private $sessionRepository;

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    public function __invoke(DisableQuestionMessage $disableQuestionMessage): void
    {
        $id = $disableQuestionMessage->id;

        $session = $this->sessionRepository->find($id);

        if (!$session) {
            throw new \InvalidArgumentException('Session not found');
        }

        $session->setActiveQuestion(null);
        $this->sessionRepository->save($session);
    }
}
