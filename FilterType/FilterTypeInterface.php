<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\FilterType;

use Symfony\Component\HttpFoundation\Request;

/**
 * FilterTypeInterface
 */
interface FilterTypeInterface
{
    /**
     * @param Request $request  The request
     * @param array   &$data    The data
     * @param string  $uniqueId The unique identifier
     */
    public function bindRequest(array &$data, $uniqueId);

    /**
     * @param array  $data     Data
     * @param string $uniqueId The identifier
     */
    public function apply(array $data, $uniqueId, $alias, $col);

    /**
     * @return string
     */
    public function getTemplate();
}
