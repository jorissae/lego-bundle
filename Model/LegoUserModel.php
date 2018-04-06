<?php

namespace Idk\LegoBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;
/**
 *
 * @MappedSuperclass
 */
class LegoUserModel implements UserInterface, EquatableInterface
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    protected $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    protected $email;

    /**
     * @var integer
     *
     * @ORM\Column(name="username", type="string", nullable=true)
     */
    protected $username;

    /**
     * @var integer
     *
     * @ORM\Column(name="enable", type="boolean", nullable=true)
     */
    protected $enable;

    /**
     * @var integer
     *
     * @ORM\Column(name="roles", type="array", nullable=true)
     */
    protected $roles;

    /**
     * @var integer
     *
     * @ORM\Column(name="password", type="string", nullable=true)
     */
    protected $password;

    /**
     * @var integer
     *
     * @ORM\Column(name="salt", type="string", nullable=true)
     */
    protected $salt;

    public function __construct() {
        $this->roles = array("ROLE_USER");
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
    }

    public function getRoles() {
        return $this->roles;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getSalt() {
        return $this->salt;
    }

    public function getUsername() {
        return $this->username;
    }

    public function eraseCredentials() {
    }

    /**
     * @param int $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @param int $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @param int $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @param int $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }



    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param int $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param int $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPlainPassword($password){
        $this->password = password_hash($password, PASSWORD_BCRYPT, ['cost'=>13,'salt'=>$this->salt]);
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof LegoUserModel) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    /**
     * @return int
     */
    public function getEnable()
    {
        return $this->enable;
    }

    /**
     * @param int $enable
     */
    public function setEnable($enable)
    {
        $this->enable = $enable;
    }




}
