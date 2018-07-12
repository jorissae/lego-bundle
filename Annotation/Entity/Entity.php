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
class Entity
{
    private $name;

    public function __construct(array $options = [])
    {
        $this->name = $options['name'] ?? null;
        $this->permissions = $options['permissions'] ?? [];
        $this->config = $options['config'] ?? null;
        $this->title = $options['title'] ?? null;
        $this->icon = $options['icon'] ?? 'rocket';
    }

    public function getRoles($suffixRoute): array{
        $perms = $this->permissions[$suffixRoute] ?? [];
        if($perms && !is_array($perms)) $perms = [$perms];
        return array_merge($perms,$this->getGlobalRoles());
    }

    public function getGlobalRoles(){
        $perms = $this->permissions['global'] ?? $this->permissions['globals'] ?? $this->permissions['all'] ?? [];
        return ($perms && !is_array($perms))?  [$perms]:$perms;
    }

    public function getConfig(){
        return $this->config;
    }

    public function getName(){
        return $this->name;
    }

    public function getTitle(){
        return $this->title;
    }

    public function getIcon(){
        return $this->icon;
    }

}