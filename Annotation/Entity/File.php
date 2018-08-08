<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

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