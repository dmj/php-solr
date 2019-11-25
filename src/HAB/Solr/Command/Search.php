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

use HAB\Solr\Response\Json\RecordCollection;
use HAB\Solr\ParamBag;

use ArrayObject;
use LogicException;
use RuntimeException;

/**
 * Search command.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class Search implements CommandInterface
{
    /**
     * Handler.
     *
     * @var string
     */
    private $handler;

    /**
     * Query.
     *
     * @var ArrayObject
     */
    private $query;

    /**
     * Result.
     *
     * @var RecordCollection
     */
    private $result;

    /**
     * Query builder.
     *
     * @var QueryBuilderInterface
     */
    private $builder;

    public function __construct ($handler, QueryBuilderInterface $builder = null)
    {
        $this->handler = ltrim($handler, '/');
        $this->builder = $builder ?: new QueryBuilder();
    }

    /**
     * {@inheritDoc}
     */
    public function getHandler ()
    {
        return $this->handler;
    }

    /**
     * {@inheritDoc}
     */
    public function getParameters ()
    {
        return $this->builder->buildQuery($this->query);
    }

    /**
     * {@inheritDoc}
     */
    public function setResponse ($response)
    {
        $result = json_decode($response, true);
        if (!is_array($result)) {
            throw new RuntimeException('Invalid response from upstream server');
        }
        $this->result = new RecordCollection($result);
    }

    /**
     * {@inheritDoc}
     */
    public function getResult ()
    {
        if (!$this->result instanceof RecordCollection) {
            throw new LogicException('Command was not executed');
        }
        return $this->result;
    }

    public function setQuery (ArrayObject $query)
    {
        $this->query = $query;
    }

    public function getQuery ()
    {
        if (is_null($this->query)) {
            $this->setQuery(new ArrayObject());
        }
        return $this->query;
    }
}
