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

use Collator;
use SplObjectStorage;
use RecursiveIterator;
use InvalidArgumentException;

/**
 * A node in a facet value tree.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright Copyright (c) 2014-2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class FacetValueTreeNode implements RecursiveIterator
{
    /**
     * Collator instance.
     *
     * @var Collator
     */
    private static $collator;

    /**
     * Compare two nodes by label for sort.
     *
     * @see http://www.php.net/manual/en/function.usort.php
     *
     * @param  FacetValueTreeNode $n1
     * @param  FacetValueTreeNode $n2
     * @return integer
     */
    public static function compareByLabel (FacetValueTreeNode $n1, FacetValueTreeNode $n2)
    {
        $l1 = $n1->hasValue() ? $n1->getValue()->getLabel() : $n1->getLabel();
        $l2 = $n2->hasValue() ? $n2->getValue()->getLabel() : $n2->getLabel();
        $collator = self::getCollator();
        return $collator->compare($l1, $l2);
    }

    /**
     * Set Collator instance.
     *
     * @param  Collator $collator
     * @return void
     */
    public static function setCollator (Collator $collator)
    {
        self::$collator = $collator;
    }

    /**
     * Return a collator.
     *
     * @return Collator
     */
    public static function getCollator ()
    {
        if (self::$collator === null) {
            self::$collator = new Collator(null);
        }
        return self::$collator;
    }

    /**
     * Node id.
     *
     * @var string
     */
    private $id;

    /**
     * Node label.
     *
     * @var string
     */
    private $label;

    /**
     * Node value.
     *
     * @var FacetValue
     */
    private $value;

    /**
     * Child nodes.
     *
     * @var array
     */
    private $childNodes;

    /**
     * Child nodes, indexed by id.
     *
     * @var array
     */
    private $childNodesById;

    /**
     * Constructor.
     *
     * @throws InvalidArgumentException Node id must not be empty
     *
     * @param  string $id
     * @param  string $label
     * @return void
     */
    public function __construct ($id, $label = null)
    {
        if (!$id) {
            throw new InvalidArgumentException('Node id must not be empty');
        }
        $this->id = $id;
        $this->childNodes = array();
        $this->childNodesById = array();
        if ($label) {
            $this->setLabel($label);
        }
    }

    /**
     * Return node id.
     *
     * @return string
     */
    public function getId ()
    {
        return $this->id;
    }

    /**
     * Return node label.
     *
     * @return string
     */
    public function getLabel ()
    {
        return $this->label;
    }

    /**
     * Return true if node has a label.
     *
     * @return boolean
     */
    public function hasLabel ()
    {
        return !!$this->label;
    }

    /**
     * Set node label.
     *
     * @param  string $label
     * @return void
     */
    public function setLabel ($label)
    {
        $this->label = $label;
    }

    /**
     * Set node value.
     *
     * @param  FacetValue $value
     * @return void
     */
    public function setValue (FacetValue $value)
    {
        $this->value = $value;
    }

    /**
     * Return node value.
     *
     * @return FacetValue|null
     */
    public function getValue ()
    {
        return $this->value;
    }

    /**
     * Return true if the node has a value.
     *
     * @return boolean
     */
    public function hasValue ()
    {
        return !!$this->value;
    }

    /**
     * Return true if node has child nodes.
     *
     * @return boolean
     */
    public function hasChildNodes ()
    {
        return !empty($this->childNodes);
    }

    /**
     * Return true if node has a child node with given id.
     *
     * @param  string $id
     * @return boolean
     */
    public function hasChildNode ($id)
    {
        return isset($this->childNodesById[$id]);
    }

    /**
     * Return child node with given id.
     *
     * @param  string $id
     * @return FacetValueTreeNode|null
     */
    public function getChildNode ($id)
    {
        if ($this->hasChildNode($id)) {
            return $this->childNodesById[$id];
        }
    }

    /**
     * Remove child node.
     *
     * @param  string $id
     * @return void
     */
    public function unsetChildNode ($id)
    {
        if ($this->hasChildNode($id)) {
            $node = $this->getChildNode($id);
            $key  = array_search($node, $this->childNodes, true);
            unset($this->childNodesById[$id]);
            unset($this->childNodes[$key]);
        }
    }

    /**
     * Add child node.
     *
     * @param  string $id
     * @param  string $label
     * @return void
     */
    public function addChildNode ($id, $label = null)
    {
        $node = new self($id, $label);
        $this->unsetChildNode($id);
        $this->childNodesById[$id] = $node;
        $this->childNodes []= $node;
    }

    /**
     * Sort by node or value label.
     *
     * @param  boolean $reverse
     * @return void
     */
    public function sortByLabel ($reverse = false)
    {
        foreach ($this->childNodes as $node) {
            $node->sortByLabel($reverse);
        }
        usort($this->childNodes, 'self::compareByLabel');
        if ($reverse) {
            $this->childNodes = array_reverse($this->childNodes);
        }
    }

    /**
     * Sort child nodes by facet value function.
     *
     * @param  callable $sortfunc
     * @param  boolean  $reverse
     * @return void
     */
    public function sortByValueFunction ($sortfunc, $reverse = false)
    {
        $sorted  = array();
        $values  = array();
        $nodemap = new SplObjectStorage();
        foreach ($this->childNodes as $node) {
            $node->sortByValueFunction($sortfunc, $reverse);
            if ($node->hasValue()) {
                $value    = $node->getValue();
                $values []= $value;
                $nodemap->attach($value, $node);
            } else {
                $sorted []= $node;
            }
        }

        usort($values, $sortfunc);
        if ($reverse) {
            $values = array_reverse($values);
        }

        foreach ($values as $value) {
            $sorted []= $nodemap->offsetGet($value);
        }
        $this->childNodes = $sorted;
    }

    ///

    /**
     * {@inheritDoc}
     */
    public function current ()
    {
        return current($this->childNodes);
    }

    /**
     * {@inheritDoc}
     */
    public function rewind ()
    {
        reset($this->childNodes);
    }

    /**
     * {@inheritDoc}
     */
    public function valid ()
    {
        return (key($this->childNodes) !== null);
    }

    /**
     * {@inheritDoc}
     */
    public function next ()
    {
        next($this->childNodes);
    }

    /**
     * {@inheritDoc}
     */
    public function key ()
    {
        return key($this->childNodes);
    }

    /**
     * {@inheritDoc}
     */
    public function hasChildren ()
    {
        return $this->current()->hasChildNodes();
    }

    /**
     * {@inheritDoc}
     */
    public function getChildren ()
    {
        return $this->current();
    }

}