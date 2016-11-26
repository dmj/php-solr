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

namespace HAB\Solr\Facet\LabelFactory;

/**
 * Uses a map of values to labels.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright Copyright (c) 2014-2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class MapLabelFactory implements LabelFactoryInterface
{
    /**
     * Translation map.
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
    public function createLabel ($value)
    {
        if ($value && isset($this->map[$value])) {
            return $this->map[$value];
        }
        return $value;
    }
}