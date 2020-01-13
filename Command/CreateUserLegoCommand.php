<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Idk\LegoBundle\Service\GlobalsParametersProvider;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

final class CreateUserLegoCommand extends Command
{

    private $parametersProvider;
    private $em;
    private $encoderFactory;

    public function __construct(GlobalsParametersProvider $parametersProvider, EntityManagerInterface $em, EncoderFactoryInterface $encoderFactory)
    {
        parent::__construct('idk:create:user');
        $this->parametersProvider = $parametersProvider;
        $this->encoderFactory = $encoderFactory;
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setName('idk:user:create')
            ->setDescription('Create a new user');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $class = $this->parametersProvider->getUserClass();
        $user = new $class();
        $question = new ConfirmationQuestion('Enabled  yes|no ? [yes]', true);
        $enabled = $helper->ask($input, $output, $question);
        $question = new Question('username  ? ');
        $username = $helper->ask($input, $output, $question);
        $question = new Question('password  ? ');
        $password = $helper->ask($input, $output, $question);
        $question = new Question('name  ? ');
        $name = $helper->ask($input, $output, $question);
        $question = new Question('email  ? ');
        $email = $helper->ask($input, $output, $question);

        $user->setPlainPassword($password);
        $user->setUsername($username);
        $user->setName($name);
        $user->setEnable($enabled);
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);

        $this->em->persist($user);
        $this->em->flush();

    }

}
