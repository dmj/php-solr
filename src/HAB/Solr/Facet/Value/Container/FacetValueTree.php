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

use RecursiveIteratorIterator;
use IteratorIterator;
use ArrayIterator;

/**
 * A tree of facet values.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright Copyright (c) 2014-2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class FacetValueTree implements ContainerInterface
{
    /**
     * Root node.
     *
     * @var FacetValueTreeNode
     */
    private $rootNode;

    /**
     * Path factory.
     *
     * @var PathFactory\PathFactoryInterface
     */
    private $pathFactory;

    /**
     * Facet values, as list.
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
     * @param  FacetValueTreePathFactory $pathFactory
     * @return void
     */
    public function __construct (PathFactory\PathFactoryInterface $pathFactory)
    {
        $this->pathFactory = $pathFactory;
        $this->rootNode    = new FacetValueTreeNode(uniqid(true));
        $this->values      = array();
    }

    /**
     * Return the root node.
     *
     * @return FacetValueTreeNode
     */
    public function getRootNode ()
    {
        return $this->rootNode;
    }

    /**
     * Update internal state.
     *
     * Collects all facet values from descendant nodes.
     *
     * @return void
     */
    public function update ()
    {
        $this->values = array();
        $iter = new RecursiveIteratorIterator($this->rootNode, RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iter as $node) {
            if ($node->hasValue()) {
                $this->values []= $node->getValue();
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function addFacetValue (FacetValue $value)
    {
        // Silently skip values w/o path
        if ($this->pathFactory->encodesFacetValueTreePath($value)) {
            if ($value->isSelected()) {
                $this->hasSelectedValue = true;
            }
            $path = $this->pathFactory->createFacetValueTreePath($value);
            $this->insert($path, $value);
        }
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
    public function sortByLabel ($reverse = false)
    {
        $this->rootNode->sortByLabel($reverse);
        $sortfunc = array('HAB Solr\Search\Facet\Value\FacetValue', 'compareByLabel');
        usort($this->values, $sortfunc);
        if ($reverse) {
            $this->values = array_reverse($this->values);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function sortByCount ($reverse = false)
    {
        $sortfunc = array('HAB Solr\Search\Facet\Value\FacetValue', 'compareByCount');
        $this->sortByFunction($sortfunc, $reverse);
    }

    /**
     * {@inheritDoc}
     */
    public function count ()
    {
        return count($this->values);
    }

    /**
     * Insert value into the tree.
     *
     * @param  array      $path
     * @param  FacetValue $value
     * @return void
     */
    private function insert (array $path, FacetValue $value)
    {
        $node = $this->rootNode;
        reset($path);
        while ($segment = current($path)) {
            if (!$node->hasChildNode($segment)) {
                $node->addChildNode($segment);
            }
            $node = $node->getChildNode($segment);
            next($path);
        }
        $node->setValue($value);
        $this->values []= $value;
    }

    /**
     * Sort values by function.
     *
     * @param  callable $sortfunc
     * @param  boolean  $reverse
     * @return void
     */
    private function sortByFunction ($sortfunc, $reverse)
    {
        usort($this->values, $sortfunc);
        if ($reverse) {
            $this->values = array_reverse($this->values);
        }
        $this->rootNode->sortByValueFunction($sortfunc, $reverse);
    }

}
