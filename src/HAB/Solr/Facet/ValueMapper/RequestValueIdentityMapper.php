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
 * A request value mapper that performs the identity transformation.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright Copyright (c) 2013-2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class RequestValueIdentityMapper implements RequestValueMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public function mapFromRequest ($values)
    {
        if (!is_array($values)) {
            return array();
        }
        return $values;
    }

    /**
     * {@inheritDoc}
     */
    public function mapToRequest (array $values)
    {
        return $values;
    }

    /**
     * {@inheritDoc}
     */
    public function mapToRequestSingleValue ($value)
    {
        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function mapFromRequestSingleValue ($value)
    {
        return $value;
    }

}