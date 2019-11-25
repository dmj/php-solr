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

namespace HAB\Solr\Facet\ValueMapper;

/**
 * A request value mapper maps filter values from request to the
 * internal representation.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright Copyright (c) 2013-2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
interface RequestValueMapperInterface
{
    /**
     * Map request values to internal representation.
     *
     * @param  mixed $values
     * @return array
     */
    public function mapFromRequest ($values);

    /**
     * Map internal representation to request values.
     *
     * @param  array $values
     * @return mixed
     */
    public function mapToRequest (array $values);

    /**
     * Map a single value from request to internal representation.
     *
     * @param  string $value
     * @return string|null
     */
    public function mapFromRequestSingleValue ($value);

    /**
     * Map a single value from  internal representation to request.
     *
     * @param  string $value
     * @return string|null
     */
    public function mapToRequestSingleValue ($value);
}
