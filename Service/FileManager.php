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


use Symfony\Component\HttpFoundation\Response;


class FileManager{
    private $container;
    private $ignore = array();

    public function __construct($container) {
        $this->container = $container;
    }

    public function getRootDir(){
        return $this->container->get('kernel')->getRootDir().'/../';
    }

    public function setIgnore($ignore){
        $this->ignore = $ignore;
    }

    public function ls($path){
        $return = array();
        $dir = opendir($this->getRootDir().$path);

        while($file = readdir($dir)) {
            if(!in_array($file,$this->ignore)){
                $realPath = $this->getRootDir().$path.'/'.$file;
                $return[] = array(
                    'name'=>$file,
                    'real_path'=>$realPath,
                    'path' => $path.'/'.$file,
                    'dir'=>is_dir($realPath),
                    'mime_type' => mime_content_type($realPath),
                    'size' => filesize($realPath),
                );
            }
        } 
        closedir($dir);
        return $return;
    }

    public function rm($path){
        $realPath = $this->getRootDir().$path;
        if(is_dir($realPath)){
            rmdir($realPath);
        }else{
            unlink($realPath);
        }
        
    }



}

        

        
        
        
        