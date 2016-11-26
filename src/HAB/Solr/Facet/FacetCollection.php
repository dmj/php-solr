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

namespace HAB\Solr\Facet;

use HAB\Solr\Response\Json\RecordCollection;
use HAB\Solr\Response\Json\RecordCollectionConsumerInterface;
use HAB\Solr\ParameterProviderInterface;

use Symfony\Component\HttpFoundation\ParameterBag;

use Countable;
use ArrayIterator;
use IteratorAggregate;

/**
 * A FacetCollection aggregates FacetAdapter objects.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright Copyright (c) 2013-2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class FacetCollection implements Countable, IteratorAggregate, ParameterProviderInterface, RecordCollectionConsumerInterface
{

    /**
     * Aggregated facets.
     *
     * @var array
     */
    private $facets;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct ()
    {
        $this->facets = array();
    }

    /**
     * Add a facet.
     *
     * @param  FacetAdapterInterface $facet
     * @return void
     */
    public function addFacet (FacetAdapterInterface $facet)
    {
        $name = $facet->getName();
        $this->facets[$name] = $facet;
    }

    /**
     * Return facet by name.
     *
     * @return FacetDecorator|null
     */
    public function getFacet ($name)
    {
        return isset($this->facets[$name]) ? $this->facets[$name] : null;
    }

    /**
     * Return true if collection aggregates facet with name.
     *
     * @param  string $name
     * @return boolean
     */
    public function hasFacet ($name)
    {
        return isset($this->facets[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator ()
    {
        return new ArrayIterator($this->facets);
    }

    /**
     * {@inheritDoc}
     */
    public function setComponentState (ParameterBag $state)
    {
        foreach ($this->facets as $facet) {
            $facet->setComponentState($state);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getComponentState ()
    {
        $params = array();
        foreach ($this->facets as $facet) {
            $params = array_merge_recursive($params, $facet->getComponentState());
        }
        return $params;
    }

    /**
     * {@inheritDoc}
     */
    public function getSearchParameters ()
    {
        $params = array();
        foreach ($this->facets as $facet) {
            $params = array_merge_recursive($params, $facet->getSearchParameters());
        }
        return $params;
    }

    /**
     * {@inheritDoc}
     */
    public function setRecordCollection (RecordCollection $response)
    {
        foreach ($this->facets as $name => $facet) {
            $facet->setRecordCollection($response);
        }
    }

    /**
     * Return number of aggregated facets.
     *
     * @return integer
     */
    public function count ()
    {
        return count($this->facets);
    }

    /**
     * Return aggregated facet count.
     *
     * @return integer
     */
    public function getAggregatedCount ()
    {
        $sum = 0;
        foreach ($this->facets as $facet) {
            $sum += $facet->getFacetValueContainer()->getAggregatedCount();
        }
        return $sum;
    }
}
