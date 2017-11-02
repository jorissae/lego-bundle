<?php

namespace Idk\LegoBundle\Command;

use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use Symfony\Component\Console\Question\Question;
use Sensio\Bundle\GeneratorBundle\Command\AutoComplete\EntitiesAutoCompleter;
use Idk\LegoBundle\Generator\LegoGenerator;
use Idk\LegoBundle\Helper\GeneratorUtils;


class GenerateLegoCommand extends GenerateDoctrineCommand
{


    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputOption('entity', 'ent', InputOption::VALUE_REQUIRED, 'The entity class name to create an LEGO for (shortcut notation)'),
                new InputOption('generateFormType', 'gft', InputOption::VALUE_REQUIRED, 'yes for create Form type (shortcut notation)')
            ))
            ->setDescription('Generates a IDKLEGO')
            ->setHelp(<<<EOT
The <info>idk:generate:lego</info> command generates an Lego for a Doctrine ORM entity.

<info>php app/console idk:generate:lego Bundle:Entity</info>
EOT
            )
            ->setName('idk:generate:lego');
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

        $dialog->writeSection($output, 'Lego Generation');

        $parts = explode('\\', $entity);
        $entityClass = array_pop($parts);
        $label = $entity;


        $generator = $this->getGenerator($this->getApplication()->getKernel()->getBundle("IdkLegoBundle"));
        $generator->setDialog($dialog);

        $generator->generate($bundle, $entityClass, $metadata[0], $output, $input, $label);
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
        $questionHelper->writeSection($output, 'Welcome to the LEGO generator');

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
                'This command helps you to generate an lego for your entity.',
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
            $input->setOption('entity', $entity);
        }

        $questionHelper = $this->getQuestionHelper();
        $input->setOption('generateFormType', 'no');
        $question = new Question($questionHelper->getQuestion('Would you like to generate FormType ? (yes or no) ', $input->getOption('generateFormType')), $input->getOption('generateFormType'));
        $generateFormType = $questionHelper->ask($input, $output, $question);
        $input->setOption('generateFormType', $generateFormType);

    }

   



    protected function createGenerator()
    {
        return new LegoGenerator($this->getContainer()->get('filesystem'), GeneratorUtils::getFullSkeletonPath('lego'));
    }
}
