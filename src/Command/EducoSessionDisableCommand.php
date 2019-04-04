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

namespace App\Command;

use App\Message\Session\DisableSessionMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class EducoSessionDisableCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'educo:session:disable';
    /**
     * @var MessageBusInterface
     */
    private $bus;

    public function __construct(MessageBusInterface $bus)
    {
        parent::__construct();

        $this->bus = $bus;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Disable a session')
            ->addArgument('id', InputArgument::REQUIRED, 'Session Id.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $io = new SymfonyStyle($input, $output);
        $sessionId = $input->getArgument('id');

        try {
            if (!is_string($sessionId)) {
                throw new \InvalidArgumentException('Invalid session id.');
            }

            $message = new DisableSessionMessage((int)$sessionId);

            $this->bus->dispatch($message);

            $io->success('Session was disabled.');
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return $e->getCode();
        }

        return 0;
    }
}
