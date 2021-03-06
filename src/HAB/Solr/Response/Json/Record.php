<?php

/**
 * Simple, schema-less SOLR record.
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2010.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category VuFind2
 * @package  Search
 * @author   David Maus <maus@hab.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */

namespace HAB\Solr\Response\Json;

use ArrayAccess;

/**
 * Simple, schema-less SOLR record.
 *
 * This record primarily servers as an example or blueprint for a schema-less
 * record. All SOLR fields are exposed via object properties.
 *
 * @category VuFind2
 * @package  Search
 * @author   David Maus <maus@hab.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class Record implements ArrayAccess
{

    /**
     * SOLR fields.
     *
     * @var array
     */
    protected $fields;

    /**
     * Constructor.
     *
     * @param array $fields SOLR document fields
     *
     * @return void
     */
    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists ($name)
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet ($name)
    {
        if (array_key_exists($name, $this->fields)) {
            return $this->fields[$name];
        } else {
            return null;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet ($name, $value)
    {
        $this->fields[$name] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset ($name)
    {
        unset($this->fields[$name]);
    }

}