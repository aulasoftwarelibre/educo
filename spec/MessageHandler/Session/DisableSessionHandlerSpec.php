<?php

namespace spec\App\MessageHandler\Session;

use App\Entity\Session;
use App\Message\Session\DisableSessionMessage;
use App\MessageHandler\Session\DisableSessionHandler;
use App\Repository\SessionRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DisableSessionHandlerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(DisableSessionHandler::class);
    }

    function let(SessionRepository $repository)
    {
        $this->beConstructedWith($repository);
    }

    function it_can_disable_a_session(DisableSessionMessage $message,SessionRepository $repository , Session $session)
    {
       $message->getId()->willReturn(3);
       $repository->find(3)->willReturn($session);

       $session->setIsActive(false)->shouldBeCalled();
       $repository->save($session)->shouldBeCalled();

        $this($message);

    }
}
