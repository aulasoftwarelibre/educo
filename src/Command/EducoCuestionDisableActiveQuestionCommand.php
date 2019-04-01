<?php

namespace App\Command;

use App\Message\Session\DisableQuestionMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class EducoCuestionDisableActiveQuestionCommand extends Command
{
    protected static $defaultName = 'educo:cuestion:disable-active-question';

    /**
     * @var MessageBusInterface
     */
    private $bus;

    public function __construct(MessageBusInterface $bus)
    {
        parent::__construct();


        $this->bus = $bus;
    }

    protected function configure()
    {
        $this
            ->setDescription('Disable a question')
            ->addArgument('id', InputArgument::REQUIRED, 'Session id')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $sessionId = $input->getArgument('id');

        try {
            if (!is_string($sessionId)) {
                throw new \InvalidArgumentException('Invalid session id.');
            }

            $message = new DisableQuestionMessage();
            $message->id = (int) $sessionId;

            $this->bus->dispatch($message);

            $io->success('Question was disabled.');
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return $e->getCode();
        }

        return 0;

    }
}
