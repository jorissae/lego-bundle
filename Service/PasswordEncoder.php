<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

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
