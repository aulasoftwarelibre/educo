<?php

namespace App\Command;

use App\Message\Session\CloseQuestionInSessionMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class EducoSessionQuestionCloseCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'educo:session:question:close';
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
            ->setDescription('Close a question')
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

            $message = new CloseQuestionInSessionMessage();
            $message->id = (int) $sessionId;

            $this->bus->dispatch($message);

            $io->success('Question was closed.');
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return $e->getCode();
        }

        return 0;
    }
}
