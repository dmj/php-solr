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
 * Removes a prefix when mapping to request, adds prefix when mapping from request.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright Copyright (c) 2014-2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class RequestValuePrefixMapper implements RequestValueMapperInterface
{
    /**
     * Prefix.
     *
     * @var string
     */
    private $prefix;

    /**
     * Prefix length.
     *
     * @var integer
     */
    private $length;

    /**
     * Constructor.
     *
     * @param  string $prefix
     * @return void
     */
    public function __construct ($prefix)
    {
        $this->prefix = $prefix;
        $this->length = strlen($prefix);
    }

    /**
     * {@inheritDoc}
     */
    public function mapFromRequest ($values)
    {
        if (is_array($values)) {
            return array_map(array($this, 'addPrefix'), $values);
        }
        return $this->addPrefix($values);
    }

    /**
     * {@inheritDoc}
     */
    public function mapToRequest (array $values)
    {
        return array_map(array($this, 'removePrefix'), $values);
    }

    /**
     * {@inheritDoc}
     */
    public function mapToRequestSingleValue ($value)
    {
        return $this->removePrefix($value);
    }

    /**
     * {@inheritDoc}
     */
    public function mapFromRequestSingleValue ($value)
    {
        return $this->addPrefix($value);
    }

    /**
     * Remove prefix from value.
     *
     * @param  string $value
     * @return string
     */
    private function removePrefix ($value)
    {
        if (strpos($value, $this->prefix) === 0) {
            return substr($value, $this->length);
        }
        return $value;
    }

    /**
     * Add prefix to value.
     *
     * @param  string $value
     * @return string
     */
    private function addPrefix ($value)
    {
        return $this->prefix . $value;
    }
}