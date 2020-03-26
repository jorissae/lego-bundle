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
use Symfony\Component\HttpFoundation\Request;

class AbstractBulkAction implements BulkActionInterface
{

    protected $i = 0;
    protected $error = null;

    public function count(){
        return $this->i;
    }

    public function getTemplate(){
        return null;
    }

    public function check(Request $request){
        return true;
    }

    public function getTemplateParameters(){
        return [];
    }

    public function getSuccess(){
        return ['lego.bulk_entities', ['%nb%' => $this->i]];
    }

    public function getError(){
        return [$this->error ?? 'lego.bulk_error', []];
    }
}
