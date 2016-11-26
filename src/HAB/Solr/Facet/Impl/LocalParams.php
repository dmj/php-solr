<?php

/**
 * This file is part of HAB Solr.
 *
 * HAB Solr is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * HAB Solr is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HAB Solr.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright Copyright (c) 2013-2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */

namespace HAB\Solr\Facet\Impl;

use HAB\Solr\ParamBag;

/**
 * Representing SOLR local params.
 *
 * @see https://wiki.apache.org/solr/LocalParams
 *
 * @author    David Maus <maus@hab.de>
 * @copyright Copyright (c) 2013-2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class LocalParams extends ParamBag
{
    /**
     * Return local param string.
     *
     * @return string
     */
    public function __toString ()
    {
        if (!empty($this->params)) {
            $params = array();
            foreach ($this->params as $key => $values) {
                foreach ($values as $value) {
                    if (strpos($value, ' ') !== false) {
                        $value = '"' . addcslashes($value, '"') . '"';
                    }
                    $params []= "{$key}={$value}";
                }
            }
            return sprintf('{!%s}', implode(' ', $params));
        }
        return '';
    }
}