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

use App\Message\Question\EnableQuestionMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class EducoQuestionEnableCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'educo:question:enable';

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
            ->setDescription('Enable question')
            ->addArgument('id', InputArgument::REQUIRED, 'Question Id.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $io = new SymfonyStyle($input, $output);
        $questionId = $input->getArgument('id');

        try {
            if (!is_string($questionId)) {
                throw new \InvalidArgumentException('Invalid session id.');
            }

            $question = new EnableQuestionMessage((int) $questionId);

            $this->bus->dispatch($question);

            $io->success('Question has enabled.');
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return $e->getCode();
        }

        return 0;
    }
}
