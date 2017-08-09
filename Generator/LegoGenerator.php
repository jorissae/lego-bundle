<?php

namespace Idk\LegoBundle\Generator;

use Doctrine\ORM\Mapping\ClassMetadata;

use Idk\LegoBundle\Helper\GeneratorUtils;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generates all classes for an admin list
 */
class LegoGenerator extends \Sensio\Bundle\GeneratorBundle\Generator\Generator
{

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $skeletonDir;

    private $dialog;

    private $auto_correction;
    public function setDialog($dialog) {
        $this->dialog = $dialog;
    }

    /**
     * @param Filesystem $filesystem  The filesystem
     * @param string     $skeletonDir The directory of the skeleton
     */
    public function __construct(Filesystem $filesystem, $skeletonDir)
    {
        $this->filesystem = $filesystem;
        $this->skeletonDir = $skeletonDir;
        $this->auto_correction = array(
            'Libelle'   =>  'Libellé',
            'Civilite'  =>  'Civilité',
            'Annee'     =>  'Année',
            'Reference' =>  'Référence',
            'Tel'       =>  'Tél',
            'Cp'        =>  'Code postal',
            'Prenom'    =>  'Prénom',
            'Ferie'     =>  'Férié',
        );
    }

    /**
     * @param Bundle          $bundle   The bundle
     * @param string          $entity   The entity name
     * @param ClassMetadata   $metadata The meta data
     * @param OutputInterface $output
     *
     * @internal param bool $generateAdminType True if we need to specify the admin type
     *
     * @return void
     */
    public function generate(Bundle $bundle, $entity, ClassMetadata $metadata, OutputInterface $output, $label)
    {
        $parts = explode('\\', $entity);
        $entityName = array_pop($parts);
        $generateAdminType = !method_exists($entity, 'getAdminType');

        if ($generateAdminType) {
            try {
                $this->generateAdminType($bundle, $entityName, $metadata);
                $output->writeln('Generating the Type code: <info>OK</info>');
            } catch (\Exception $error) {
                $output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
                $output->writeln('Generating the Type code: <error>ERROR</error>');
            }
        }

        try {
            $this->generateConfiguration($bundle, $entityName, $metadata, $generateAdminType, $label);
            $output->writeln('Generating the Configuration code: <info>OK</info>');
        } catch (\Exception $error) {
            $output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
            $output->writeln('Generating the Configuration code: <error>ERROR</error>');
        }


        try {
            $this->generateController($bundle, $entityName, $metadata);
            $output->writeln('Generating the Controller code: <info>OK</info>');
        } catch (\Exception $error) {
            $output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
            $output->writeln('Generating the Controller code: <error>ERROR</error>');
        }

        try {
            $this->generateBehatTests($bundle, $entityName, $metadata, $label);
            $output->writeln('Generating the Test code: <info>OK</info>');
        } catch (\Exception $error) {
            $output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
            $output->writeln('Generating the Test code: <error>ERROR</error>');
        }        
    }

    /**
     * @param Bundle        $bundle            The bundle
     * @param string        $entityName        The entity name
     * @param ClassMetadata $metadata          The meta data
     * @param boolean       $generateAdminType True if we need to specify the admin type
     *
     * @throws \RuntimeException
     * @return void
     */
    public function generateConfiguration(Bundle $bundle, $entityName, ClassMetadata $metadata, $generateAdminType, $label)
    {
        $className = sprintf("%sAdminListConfigurator", $entityName);
        $dirPath = sprintf("%s/AdminList", $bundle->getPath());
        $classPath = sprintf("%s/%s.php", $dirPath, str_replace('\\', '/', $className));

        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $className, $classPath));
        }
        $this->setSkeletonDirs(array($this->skeletonDir));
        $this->renderFile('/AdminList/AdminListConfigurator.php', $classPath, array(
            'namespace'           => $bundle->getNamespace(),
            'bundle'              => $bundle,
            'entity_class'        => $entityName,
            'fields'              => $this->getFieldsWithFilterTypeFromMetadata($metadata),
            'generate_admin_type' => $generateAdminType,
            'label'               => $label
        ));
    }

    /**
     * @param Bundle $bundle     The bundle
     * @param string $entityName The entity name
     *
     * @throws \RuntimeException
     */
    public function generateController(Bundle $bundle, $entityName)
    {
        $className = sprintf("%sAdminListController", $entityName);
        $dirPath = sprintf("%s/Controller", $bundle->getPath());
        $classPath = sprintf("%s/%s.php", $dirPath, str_replace('\\', '/', $className));
        $extensions = 'csv';
        if (class_exists("\\Lle\\AdminListBundle\\Service\\ExportService")) {
            $extensions = implode('|', \Lle\AdminListBundle\Service\ExportService::getSupportedExtensions());
        }

        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $className, $classPath));
        }

        $this->setSkeletonDirs(array($this->skeletonDir));
        $this->renderFile('/Controller/EntityAdminListController.php', $classPath, array(
            'namespace'         => $bundle->getNamespace(),
            'bundle'            => $bundle,
            'entity_class'      => $entityName,
            'export_extensions' => $extensions
        ));

    }

    /**
     * @param Bundle        $bundle     The bundle
     * @param string        $entityName The entity name
     * @param ClassMetadata $metadata   The meta data
     *
     * @throws \RuntimeException
     */
    public function generateAdminType(Bundle $bundle, $entityName, ClassMetadata $metadata)
    {
        $className = sprintf("%sAdminType", $entityName);
        $dirPath = sprintf("%s/Form", $bundle->getPath());
        $classPath = sprintf("%s/%s.php", $dirPath, str_replace('\\', '/', $className));

        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $className, $classPath));
        }

        $this->setSkeletonDirs(array($this->skeletonDir));
        $this->renderFile('/Form/EntityAdminType.php', $classPath, array(
            'namespace'         => $bundle->getNamespace(),
            'bundle'            => $bundle,
            'entity_class'      => $entityName,
            'fields'            => $this->getFieldsForType($metadata)
        ));
    }


    /**
     * @param Bundle          $bundle
     * @param OutputInterface $output
     * @param array           $parameters
     */
    public function generateBehatTests(Bundle $bundle, $entityName, ClassMetadata $metadata, $label)
    {

        $className = strtolower($entityName);
        $dirPath = sprintf("%s/Features", $bundle->getPath());
        $featurePath = sprintf("%s/%s.feature", $dirPath, str_replace('\\', '/', $className));

        if (file_exists($featurePath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $className, $classPath));
        }
        $this->setSkeletonDirs(array($this->skeletonDir));
        $this->renderFile('/Features/adminlist.feature', $featurePath, array(
            'namespace'           => $bundle->getNamespace(),
            'bundle'              => $bundle,
            'entity_class'        => $entityName,
            'fields'              => $this->getFieldsWithFilterTypeFromMetadata($metadata),
            'label'               => $label
        ));
    }

    /**
     * @param ClassMetadata $metadata
     *
     * @return string[]
     */
    private function getFieldsFromMetadata(ClassMetadata $metadata)
    {
        return GeneratorUtils::getFieldsFromMetadata($metadata);
    }

    /**
     * @param ClassMetadata $metadata
     *
     * @return array
     */
    private function getFieldsWithFilterTypeFromMetadata(ClassMetadata $metadata)
    {
        $mapping = array(
            'string' => 'ORM\StringFilterType',
            'text' => 'ORM\StringFilterType',
            'integer' => 'ORM\NumberFilterType',
            'smallint' => 'ORM\NumberFilterType',
            'bigint' => 'ORM\NumberFilterType',
            'decimal' => 'ORM\NumberFilterType',
            'boolean' => 'ORM\BooleanFilterType',
            'date' => 'ORM\DateFilterType',
            'datetime' => 'ORM\DateFilterType',
            'time' => 'ORM\DateFilterType'
        );

        $fields = array();
        
        foreach ($this->getFieldsFromMetadata($metadata) as $fieldName) {
            $type = $metadata->getTypeOfField($fieldName);
            $filterType = isset($mapping[$type]) ? $mapping[$type] : null;

            preg_match_all('/((?:^|[A-Z])[a-z]+)/', $fieldName, $matches);
            $fieldTitle = ucfirst(strtolower(implode(' ', $matches[0])));
            if (array_key_exists($fieldTitle, $this->auto_correction)) {
                $fieldTitle = $this->auto_correction[$fieldTitle];
            }
            

            if (!is_null($filterType)) {
                $fields[$fieldName] = array('filterType' => $filterType, 'fieldTitle' => $fieldTitle, 'fieldType'=>$type);
            }
        }
        return $fields;
    }

    /**
     * @param ClassMetadata $metadata
     *
     * @return array
     */
    private function getFieldsForType(ClassMetadata $metadata)
    {
        $fields = array();
        
        foreach ($this->getFieldsFromMetadata($metadata) as $fieldName) {
            $type = $metadata->getTypeOfField($fieldName);
            preg_match_all('/((?:^|[A-Z])[a-z]+)/', $fieldName, $matches);
            $fieldTitle = ucfirst(strtolower(implode(' ', $matches[0])));
            if (array_key_exists($fieldTitle, $this->auto_correction)) {
                $fieldTitle = $this->auto_correction[$fieldTitle];
            }
            $formType = null;
            if($type == 'date' or $type == 'datetime') $formType = 'lle_date';
            $fields[$fieldName] = array('fieldTitle' => $fieldTitle,'fieldType'=>$type,'formType'=>$formType);
        }
        return $fields;
    }

}
