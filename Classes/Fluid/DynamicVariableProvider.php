<?php
/*
 * PULPIT
 * A sermon plugin for WordPress
 *
 * Copyright (c) 2018 Christoph Fischer, http://www.peregrinus.de
 * Author: Christoph Fischer, chris@toph.de
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Peregrinus\Pulpit\Fluid;

use Peregrinus\Pulpit\Debugger;
use Peregrinus\Pulpit\Domain\Model\AbstractModel;
use TYPO3Fluid\Fluid\Core\Variables\StandardVariableProvider;
use TYPO3Fluid\Fluid\Core\Variables\VariableProviderInterface;

class DynamicVariableProvider extends StandardVariableProvider implements VariableProviderInterface
{
    /**
     * Get a variable by dotted path expression, retrieving the
     * variable from nested arrays/objects one segment at a time.
     * If the second variable is passed, it is expected to contain
     * extraction method names (constants from VariableExtractor)
     * which indicate how each value is extracted.
     *
     * @param string $path
     * @return mixed
     */
    public function getByPath($path, array $accessors = [])
    {
        // get first path element
        $elements = explode('.', $path);
        $object = $this->variables[$elements[0]];
        $lastElement = $elements[0];
        $debug = [];
        unset($elements[0]);

        // traverse path
        foreach ($elements as $element) {
            if (is_object($object)) {
                if (property_exists($object, $element)) {
                    $object = $object->$element;
                    $debug[$element] = $lastElement.'->'.$element;
                } else {
                    $getter = 'get' . ucfirst($element);
                    $object = $object->$getter();
                    $debug[$element] = $lastElement.'->'.$getter.'()';
                }
            } elseif (is_array($object)) {
                $object = $object[$element];
                $debug[$element] = $lastElement.'['.$element.']';
            } else {
                $object = '';
            }
            $lastElement = $element;
        }
        return $object;
    }

    /**
     * @param string $identifier
     * @return boolean
     */
    public function exists($identifier)
    {
        return ($identifier === 'incrementer' || $identifier === 'random' || parent::exists($identifier));
    }

}
