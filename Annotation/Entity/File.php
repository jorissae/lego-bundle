<?php
namespace Idk\LegoBundle\Annotation\Entity;

/**
 * @Annotation
 */
class File
{

    private $directory;

    public function __construct(array $options = [])
    {
        $this->directory = isset($options['directory'])? $options['directory']:'web/uploads/files';
    }

    public function getDirectory(){
        return $this->directory;
    }

}