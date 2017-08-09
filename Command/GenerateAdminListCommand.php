<?php

namespace Idk\LegoBundle\Command;

use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Sensio\Bundle\GeneratorBundle\Command\AutoComplete\EntitiesAutoCompleter;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Idk\LegoBundle\Generator\LegoGenerator;
use Idk\LegoBundle\Helper\GeneratorUtils;

/**
 * Generates a LleAdminList
 */
class GenerateAdminListCommand extends GenerateDoctrineCommand
{


    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputOption('entity', '', InputOption::VALUE_REQUIRED, 'The entity class name to create an admin list for (shortcut notation)'),
                new InputOption('navigation', '', InputOption::VALUE_REQUIRED, 'Add the adminlist link in navigation.yml', 'no'),
                new InputOption('security', '', InputOption::VALUE_REQUIRED, 'Add the roles in security.yml role hierarchy', 'no')
                ))
            ->setDescription('Generates a LleAdminList')
            ->setHelp(<<<EOT
The <info>idk:generate:adminlist</info> command generates an AdminList for a Doctrine ORM entity.

<info>php app/console lle:generate:adminlist Bundle:Entity</info>
EOT
            )
            ->setName('lle:generate:adminlist');
    }

    /**
     * Executes the command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @throws \RuntimeException
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getQuestionHelper();

        GeneratorUtils::ensureOptionsProvided($input, array('entity'));

        $entity = Validators::validateEntityName($input->getOption('entity'));
        list($bundle, $entity) = $this->parseShortcutNotation($entity);

        $entityClass = $this->getContainer()->get('doctrine')->getAliasNamespace($bundle).'\\'.$entity;
        $metadata    = $this->getEntityMetadata($entityClass);
        $bundle      = $this->getContainer()->get('kernel')->getBundle($bundle);

        $dialog->writeSection($output, 'AdminList Generation');

        $parts = explode('\\', $entity);
        $entityClass = array_pop($parts);

        $navigation = $input->getOption('navigation');
        $label = $entity;
        if($navigation != 'no') {
            $label = $this->updateNavigation($dialog, $input, $output, $bundle, $entity);
        }
        $security = $input->getOption('security');
        if($security != 'no') {
            $this->updateSecurity($dialog, $input, $output, $bundle, $entity);
        }

        $generator = $this->getGenerator($this->getApplication()->getKernel()->getBundle("IdkLegoBundle"));
        $generator->setDialog($dialog);
        $generator->generate($bundle, $entityClass, $metadata[0], $output, $label);
    }

    /**
     * Interacts with the user.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();
        $questionHelper->writeSection($output, 'Welcome to the Lle admin list generator');

        // entity
        $entity = null;
        try {
            $entity = $input->getOption('entity') ? Validators::validateEntityName($input->getOption('entity')) : null;
        } catch (\Exception $error) {
            $output->writeln($questionHelper->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }

        if (is_null($entity)) {
            $output->writeln(array(
                '',
                'This command helps you to generate an admin list for your entity.',
                '',
                'You must use the shortcut notation like <comment>AcmeBlogBundle:Post</comment>.',
                '',
            ));
            $question = new Question($questionHelper->getQuestion('The Entity shortcut name', $input->getOption('entity')), $input->getOption('entity'));
            $question->setValidator(array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateEntityName'));

            $autocompleter = new EntitiesAutoCompleter($this->getContainer()->get('doctrine')->getManager());
            $autocompleteEntities = $autocompleter->getSuggestions();
            $question->setAutocompleterValues($autocompleteEntities);
            $entity = $questionHelper->ask($input, $output, $question);
            //$entity = $dialog->askAndValidate($output, $dialog->getQuestion('The entity shortcut name', $entity), array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateEntityName'), false, $entity);
            $input->setOption('entity', $entity);
        }

        // Navigation
        $navigation = $input->getOption('navigation');
        $output->writeln(array(
            '',
            'Determine if you want to add the adminlist link in your navigation.yml.',
            '',
        ));
        $question = new Question($questionHelper->getQuestion('Would you like to add adminlist link in your navigation ? (yes or no) ', $navigation), $navigation);
        $navigation = $questionHelper->ask($input, $output, $question);
        $input->setOption('navigation', $navigation);

        // Security
        $navigation = $input->getOption('security');
        $output->writeln(array(
            '',
            'Determine if you want to add the role in your security.yml.',
            '',
        ));
        $question = new Question($questionHelper->getQuestion('Would you like to add the role in your security.yml ? (yes or no) ', $navigation), $navigation);
        $security = $questionHelper->ask($input, $output, $question);
        $input->setOption('security', $security);

    }

   

    protected function updateNavigation(QuestionHelper $questionHelper, InputInterface $input, OutputInterface $output, $bundle, $entity)
    {
        $label = $entity.'s';
        if ($input->isInteractive()) {
            $question = new Question($questionHelper->getQuestion('Label of the link ', $label), $label);
            $question_label = $questionHelper->ask($input, $output, $question);
            $label = $question_label;
        }
        $file = 'app/config/navigation.yml';
        $current = file_get_contents($file);
        $route = strtolower($bundle->getName().'_admin_'.$entity);
        if (false !== strpos($current, $route) ) {
            throw new \RuntimeException(sprintf('Already imported in navigation.yml.'));
        }

        $output->write('Adding link to navigation.yml : ');
        $code = $current;
        $code .= sprintf("\n                %s:\n", $entity);
        $code .= sprintf("                    label: %s\n", $label);
        $code .= sprintf("                    route: %s\n", $route);
        $code .= sprintf("                    roles: [ROLE_ADMIN_%s_LIST]\n", strtoupper($entity));
        $code .= sprintf("                    attributes:\n");
        $code .= sprintf("                        icon: cogs");
        file_put_contents($file, $code);
        return $label;
    }


    protected function updateSecurity(QuestionHelper $questionHelper, InputInterface $input, OutputInterface $output, $bundle, $entity)
    {
        $file = 'app/config/security.yml';
        $current = file_get_contents($file);
        $role = "ROLE_ADMIN_".strtoupper($entity);

        foreach(array('_ADD','_EDIT','_SHOW','_DELETE', '_EXPORT','_LOGS','_LIST') as $suffixe ) {
            $roleArr[] = $role.$suffixe;
        }
        $output->write('Adding role to security.yml : ');
        
        $code = preg_replace("/( *ROLE_SUPER_ADMIN)/","          - ".$role."\n$1", $current);
        $code = preg_replace("/( *role_hierarchy:)/","$1\n        ".$role.": [".join(', ',$roleArr)."]", $code);

        file_put_contents($file, $code);
    }
    /**
     * LleTestBundle_TestEntity:
    resource: "@LleTestBundle/Controller/TestEntityAdminListController.php"
    type:     annotation
    prefix:   /{_locale}/admin/testentity/
    requirements:
    _locale: %requiredlocales%
     */

    protected function createGenerator()
    {
        return new LegoGenerator($this->getContainer()->get('filesystem'), GeneratorUtils::getFullSkeletonPath('adminlist'));
    }
}
