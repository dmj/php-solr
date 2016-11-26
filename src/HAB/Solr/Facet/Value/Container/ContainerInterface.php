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

use Countable;
use Iterator;

/**
 * Interface of a facet value container.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright Copyright (c) 2014-2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
interface ContainerInterface extends Countable
{
    /**
     * Return iterator to iterate over contained facet values.
     *
     * @return Iterator
     */
    public function getIterator ();

    /**
     * Return iterator to iterate over selected facet values only.
     *
     * @return Iterator
     */
    public function getSelectedValueIterator ();

    /**
     * Are there selected values?
     *
     * @return boolean
     */
    public function hasSelectedValue ();
    
    /**
     * Add facet value.
     *
     * @param  FacetValue $value
     * @return void
     */
    public function addFacetValue (FacetValue $value);

    /**
     * Sort facet values by facet value label.
     *
     * @param  boolean $reverse Sort in reverse order
     * @return void
     */
    public function sortByLabel ($reverse = false);

    /**
     * Sort facet values by facet value count.
     *
     * @param  boolean $reverse Sort in reverse order
     * @return void
     */
    public function sortByCount ($reverse = false);
}