<?php


namespace App\MessageHandler\User;


use App\Entity\User;
use App\Message\User\CreateNewAdminUserMessage;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateNewAdminUserHandler implements MessageHandlerInterface
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
 {

     $this->userRepository = $userRepository;
 }

 public function __invoke(CreateNewAdminUserMessage $message)
 {
     $user = $this->userRepository->findOneBy(['username' => $message->username]);

     if($user){
         throw new \InvalidArgumentException('User found');
     }

     $user = new User();
     $user->setUsername($message->username);
     $user->setPassword($message->password);

     $this->userRepository->save($user);

 }

}