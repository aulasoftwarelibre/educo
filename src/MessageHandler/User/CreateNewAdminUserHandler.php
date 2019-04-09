<?php


namespace App\MessageHandler\User;


use App\Entity\User;
use App\Message\User\CreateNewAdminUserMessage;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateNewAdminUserHandler implements MessageHandlerInterface
{

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserRepository $userRepository , UserPasswordEncoderInterface $encoder)
 {

     $this->userRepository = $userRepository;
     $this->encoder = $encoder;
 }

 public function __invoke(CreateNewAdminUserMessage $message)
 {
     $user = $this->userRepository->findOneBy(['username' => $message->username]);

     if($user){
         throw new \InvalidArgumentException('User found');
     }

     $user = new User();
     $user->setUsername($message->username);

     $encoded = $this->encoder->encodePassword($user, $message->password);
     $user->setPassword($encoded);

     $this->userRepository->save($user);

 }

}