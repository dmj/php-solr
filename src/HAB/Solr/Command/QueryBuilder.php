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
 * @copyright (c) 2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */

namespace HAB\Solr\Command;

use HAB\Solr\ParamBag;

/**
 * Query builder default implementation.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2017 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class QueryBuilder implements QueryBuilderInterface
{
    /**
     * Return Solr query parameters.
     *
     * @param  array|\ArrayAccess $query
     * @return ParamBag
     */
    public function buildQuery ($query)
    {
        $params = new ParamBag();
        foreach ($query as $key => $value) {
            $params->set($key, $value);
        }
        return $params;
    }
}