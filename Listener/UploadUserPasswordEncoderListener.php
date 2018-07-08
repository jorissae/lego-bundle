<?php

namespace Idk\LegoBundle\Listener;

use Idk\LegoBundle\Model\LegoUserModel;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UploadUserPasswordEncoderListener
{
    private $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->uploadPassword($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->uploadPassword($entity);
    }

    private function uploadPassword($user)
    {
        if($user instanceof LegoUserModel){
            if($user->getPlainPassword() && $user->getPassword() === null) {
                $encoder = $this->encoderFactory->getEncoder($user);
                if ($encoder instanceof BCryptPasswordEncoder) {
                    $user->setSalt(null);
                } else {
                    $salt = rtrim(str_replace('+', '.', base64_encode(random_bytes(32))), '=');
                    $user->setSalt($salt);
                }
                $hashedPassword = $encoder->encodePassword($user->getPlainPassword(), $user->getSalt());
                $user->setPassword($hashedPassword);
            }
        }

    }
}