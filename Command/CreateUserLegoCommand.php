<?php

namespace Idk\LegoBundle\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Command\Command;

final class CreateUserLegoCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('idk:create:user')
            ->setDescription('Create a new user');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em =  $this->getContainer()->get('doctrine')->getManager();
        $helper = $this->getHelper('question');


        $question = new ConfirmationQuestion('Enabled  ? ', false);
        $enabled = $helper->ask($input, $output, $question);
        $question = new Question('username  ? ');
        $username = $helper->ask($input, $output, $question);
        $question = new Question('password  ? ');
        $password = $helper->ask($input, $output, $question);
        $question = new Question('name  ? ');
        $name = $helper->ask($input, $output, $question);
        $question = new Question('email  ? ');
        $email = $helper->ask($input, $output, $question);


        $user = new User();
        $user->setPlainPassword($password);
        $user->setUsername($username);
        $user->setName($name);
        $user->setEnable($enabled);
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);

        $em->persist($user);
        $em->flush();

    }

}
