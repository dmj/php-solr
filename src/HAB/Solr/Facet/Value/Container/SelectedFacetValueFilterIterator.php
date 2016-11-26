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

use FilterIterator;
use Iterator;

/**
 * FilterIterator that filters selected facet values.
 *
 * Constructor argument $complement can be used to reverse the selection logic.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright Copyright (c) 2014-2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class SelectedFacetValueFilterIterator extends FilterIterator
{
    /**
     * Should we use the complement of the filter function?
     *
     * @var boolean
     */
    private $complement;

    /**
     * Constructor.
     *
     * @param  Iterator $iterator
     * @param  boolean  $complement
     * @return void
     */
    public function __construct (Iterator $iterator, $complement = false)
    {
        parent::__construct($iterator);
        $this->complement = $complement;
    }

    /**
     * {@inheritDoc}
     */
    public function accept ()
    {
        $accept = $this->current()->isSelected();
        return $this->complement ? !$accept : $accept;
    }
}