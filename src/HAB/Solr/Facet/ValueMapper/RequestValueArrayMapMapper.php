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
 * @copyright Copyright (c) 2014-2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */

namespace HAB\Solr\Facet\ValueMapper;

/**
 * Use a array map to translate request to facet values.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright Copyright (c) 2014-2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class RequestValueArrayMapMapper implements RequestValueMapperInterface
{
    /**
     * Map, indexed by request value.
     *
     * @var array
     */
    private $map;

    /**
     * Constructor.
     *
     * @param  array $map
     * @return void
     */
    public function __construct (array $map)
    {
        $this->map = $map;
    }

    /**
     * {@inheritDoc}
     */
    public function mapFromRequest ($values)
    {
        if (!is_array($values)) {
            return array();
        }
        $facetValues = array();
        foreach ($values as $requestValue) {
            if ($facetValue = $this->lookupFacetValue($requestValue)) {
                $facetValues []= $facetValue;
            }
        }
        return $facetValues;
    }

    /**
     * {@inheritDoc}
     */
    public function mapToRequest (array $values)
    {
        $requestValues = array();
        foreach ($values as $facetValue) {
            if ($requestValue = $this->lookupRequestValue($facetValue)) {
                $requestValues []= $requestValue;
            }
        }
        return $requestValues;
    }

    /**
     * {@inheritDoc}
     */
    public function mapFromRequestSingleValue ($value)
    {
        return $this->lookupFacetValue($value);
    }

    /**
     * {@inheritDoc}
     */
    public function mapToRequestSingleValue ($value)
    {
        return $this->lookupRequestValue($value);
    }

    ///

    /**
     * Return facet value for request value.
     *
     * @param  string $requestValue
     * @return string|null
     */
    private function lookupFacetValue ($requestValue)
    {
        if (isset($this->map[$requestValue])) {
            return $this->map[$requestValue];
        }
        return null;
    }

    /**
     * Return request value for facet value.
     *
     * @param  string $facetValue
     * @return string|null
     */
    private function lookupRequestValue ($facetValue)
    {
        $key = array_search($facetValue, $this->map);
        if ($key !== false) {
            return $key;
        }
        return null;
    }
}