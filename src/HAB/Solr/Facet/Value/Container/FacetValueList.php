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

namespace HAB\Solr\Facet\Value\Container;

use HAB\Solr\Facet\Value\FacetValue;

use ArrayIterator;
use IteratorIterator;

/**
 * A flat list of facet values.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright Copyright (c) 2014-2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class FacetValueList implements ContainerInterface
{
    /**
     * Facet values.
     *
     * @var array
     */
    private $values;

    /**
     * Does the list contain a selected value?
     *
     * @var boolean
     */
    private $hasSelectedValue = false;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct ()
    {
        $this->values = array();
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator ()
    {
        return new IteratorIterator(new ArrayIterator($this->values));
    }

    /**
     * {@inheritDoc}
     */
    public function getSelectedValueIterator ()
    {
        return new SelectedFacetValueFilterIterator($this->getIterator());
    }

    /**
     * {@inheritDoc}
     */
    public function hasSelectedValue ()
    {
        return $this->hasSelectedValue;
    }

    /**
     * {@inheritDoc}
     */
    public function addFacetValue (FacetValue $value)
    {
        if ($value->isSelected()) {
            $this->hasSelectedValue = true;
        }
        if (!in_array($value, $this->values, true)) {
            $this->values []= $value;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function sortByLabel ($reverse = false)
    {
        usort($this->values, array(FacetValue::class, 'compareByLabel'));
        if ($reverse) {
            $this->values = array_reverse($this->values);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function sortByCount ($reverse = false)
    {
        usort($this->values, array(FacetValue::class, 'compareByCount'));
        if ($reverse) {
            $this->values = array_reverse($this->values);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function count ()
    {
        return count($this->values);
    }
}
