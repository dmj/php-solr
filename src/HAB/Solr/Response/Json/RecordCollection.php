<?php

/**
 * Simple JSON-based record collection.
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

use Iterator;

/**
 * Simple JSON-based record collection.
 *
 * @category VuFind2
 * @package  Search
 * @author   David Maus <maus@hab.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class RecordCollection implements Iterator
{
    /**
     * Template of deserialized SOLR response.
     *
     * @see self::__construct()
     *
     * @var array
     */
    protected static $template = array(
        'responseHeader' => array(),
        'response'       => array('numFound' => 0, 'start' => 0, 'docs' => array()),
        'spellcheck'     => array('suggestions' => array()),
        'facet_counts'   => array(),
    );

    /**
     * Deserialized SOLR response.
     *
     * @var array
     */
    protected $response;

    /**
     * Facets.
     *
     * @var Facets
     */
    protected $facets;

    /**
     * Spellcheck information.
     *
     * @var Spellcheck
     */
    protected $spellcheck;

    /**
     * Response records.
     *
     * @var array
     */
    protected $records = array();

    /**
     * Array pointer
     *
     * @var int
     */
    protected $pointer = 0;

    /**
     * Zero-based offset in complete search result.
     *
     * @var int
     */
    protected $offset = 0;

    
    /**
     * Constructor.
     *
     * @param array $response Deserialized SOLR response
     *
     * @return void
     */
    public function __construct(array $response)
    {
        $this->response = array_replace_recursive(static::$template, $response);
        $this->offset = $this->response['response']['start'];
        foreach ($this->response['response']['docs'] as $record) {
            $this->add(new Record($record));
        }
        $this->rewind();
    }

    /**
     * Return records.
     *
     * @return array
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * Return first record in response.
     *
     * @return Record|null
     */
    public function first()
    {
        return isset($this->records[0]) ? $this->records[0] : null;
    }

    /**
     * Return offset in the total search result set.
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Add a record to the collection.
     *
     * @param Record $record Record to add
     *
     * @return void
     */
    public function add(Record $record)
    {
        if (!in_array($record, $this->records, true)) {
            $this->records[$this->pointer] = $record;
            $this->next();
        }
    }

    /// Iterator interface

    /**
     * Return true if current collection index is valid.
     *
     * @return boolean
     */
    public function valid()
    {
        return isset($this->records[$this->pointer]);
    }

    /**
     * Return record at current collection index.
     *
     * @return Record
     */
    public function current()
    {
        return $this->records[$this->pointer];
    }

    /**
     * Rewind collection index.
     *
     * @return void
     */
    public function rewind()
    {
        $this->pointer = 0;
    }

    /**
     * Move to next collection index.
     *
     * @return void
     */
    public function next()
    {
        $this->pointer++;
    }

    /**
     * Return current collection index.
     *
     * @return integer
     */
    public function key()
    {
        return $this->pointer + $this->getOffset();
    }

    /// Countable interface

    /**
     * Return number of records in collection.
     *
     * @return integer
     */
    public function count()
    {
        return count($this->records);
    }

    /**
     * Return spellcheck information.
     *
     * @return Spellcheck
     */
    public function getSpellcheck()
    {
        if (!$this->spellcheck) {
            $params = isset($this->response['responseHeader']['params'])
                ? $this->response['responseHeader']['params'] : array();
            $sq = isset($params['spellcheck.q'])
                ? $params['spellcheck.q']
                : (isset($params['q']) ? $params['q'] : '');
            $sugg = isset($this->response['spellcheck']['suggestions'])
                ? $this->response['spellcheck']['suggestions'] : array();
            $this->spellcheck = new Spellcheck($sugg, $sq);
        }
        return $this->spellcheck;
    }

    /**
     * Return total number of records found.
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->response['response']['numFound'];
    }

    /**
     * Return SOLR facet information.
     *
     * @return array
     */
    public function getFacets()
    {
        if (!$this->facets) {
            $this->facets = new Facets($this->response['facet_counts']);
        }
        return $this->facets;
    }

    /**
     * Get grouped results.
     *
     * @return array
     */
    public function getGroups()
    {
        if (is_null($this->groups)) {
            $this->groups = array();
            if (isset($this->response['grouped'])) {
                foreach ($this->response['grouped'] as $field => $group) {
                    $this->groups[$field] = array();
                    foreach ($group['groups'] as $data) {
                        $this->groups[$field] []= new Group($data);
                    }
                }
            }
        }
        return $this->groups;
    }

    /**
     * Return StatsComponent information.
     *
     * @return array
     */
    public function getStats ()
    {
        return isset($this->response['stats']) ? $this->response['stats'] : array();
    }

    /**
     * Get highlighting details.
     *
     * @return array
     */
    public function getHighlighting()
    {
        return isset($this->response['highlighting'])
            ? $this->response['highlighting'] : array();
    }
}