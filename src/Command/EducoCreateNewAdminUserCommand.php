<?php

namespace App\Command;

use App\Message\User\CreateNewAdminUserMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class EducoCreateNewAdminUserCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'educo:user:create:new-admin';

    private $bus;

    public function __construct(MessageBusInterface $bus)
    {
        parent::__construct();


        $this->bus = $bus;
    }

    protected function configure()
    {
        $this
            ->setDescription('Creates a new admin user.')
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the user.')
            ->addArgument('password',  InputArgument::REQUIRED , 'User password');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');

        $password = $input->getArgument('password');
        try{
            if(!is_string($username)) {
                throw new \InvalidArgumentException('Invalid username');
            }

            $message = new CreateNewAdminUserMessage();
            $message->username = $username;
            $message->password = $password;

            $this->bus->dispatch($message);


            $io->success('User created');
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return $e->getCode();
        }
            return 0;
    }


}
