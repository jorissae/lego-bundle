<?php
namespace Idk\LegoBundle\Service;


use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;


class PasswordEncoder extends BasePasswordEncoder{


    public function encodePassword($raw, $salt)
    {
        return password_hash($raw, PASSWORD_BCRYPT, ['cost' => 13, 'salt' => $salt]);
    }


    public function isPasswordValid($encoded, $raw, $salt)
    {
        return (password_hash($raw, PASSWORD_BCRYPT, ['cost' => 13, 'salt' => $salt]) == $encoded);
    }
}
