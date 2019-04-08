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

use App\Message\Session\DisableSessionMessage;
use App\Repository\SessionRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DisableSessionHandler implements MessageHandlerInterface
{
    /**
     * @var SessionRepository
     */
    private $sessionRepository;
    /**
     * @var ObjectManager
     */
    private $manager;

    public function __construct(SessionRepository $sessionRepository, ObjectManager $manager)
    {
        $this->sessionRepository = $sessionRepository;
        $this->manager = $manager;
    }

    public function __invoke(DisableSessionMessage $message): void
    {
        $id = $message->getId();
        $session = $this->sessionRepository->find($id);

        if (!$session) {
            throw new \InvalidArgumentException('Session not found');
        }

        $session->setIsActive(false);

        $this->manager->flush();
    }
}
