<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\FilterType\ORM;

use Symfony\Component\HttpFoundation\Request;

/**
 * BooleanFilterType
 */
class BooleanFilterType extends AbstractORMFilterType
{
    /**
     * @param Request $request  The request
     * @param array   &$data    The data
     * @param string  $uniqueId The unique identifier
     */
    public function bindRequest(array &$data, $uniqueId)
    {
        $data['value'] = $this->getValueSession('filter_value_' . $uniqueId);
        return ($data['value'] != '');
    }

    /**
     * @param array  $data     The data
     * @param string $uniqueId The unique identifier
     */
    public function apply(array $data, $uniqueId, $alias, $col)
    {
        if (isset($data['value'])) {
            switch ($data['value']) {
                case 'true':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->eq($alias . $col, 'true'));
                    break;
                case 'false':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->eq($alias . $col, 'false'));
                    break;
            }
        }
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'IdkLegoBundle:FilterType:booleanFilter.html.twig';
    }
}
