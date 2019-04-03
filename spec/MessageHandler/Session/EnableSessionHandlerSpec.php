<?php

namespace spec\App\MessageHandler\Session;

use App\Entity\Session;
use App\Message\Session\EnableSessionMessage;
use App\MessageHandler\Session\EnableSessionHandler;
use App\Repository\SessionRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EnableSessionHandlerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(EnableSessionHandler::class);
    }

    function let(SessionRepository $repository)
    {
        $this->beConstructedWith($repository);
    }

    function it_can_enable_a_session(Session $session , EnableSessionMessage $enableSessionMessage , SessionRepository $repository)
    {
        $enableSessionMessage->getId()->willReturn(3);
        $repository->find(3)->willReturn($session);

        $session->setIsActive(true)->shouldBeCalled();
        $repository->save($session)->shouldBeCalled();

        $this($enableSessionMessage);

    }

    function it_can_throw_a_error_if_doesnt_exist(EnableSessionMessage $enableSessionMessage, SessionRepository $repository)
    {
        $enableSessionMessage->getId()->willReturn(2);
        $repository->find(2)->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('__invoke', [$enableSessionMessage]);


    }
}
