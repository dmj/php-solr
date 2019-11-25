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
 * @copyright Copyright (c) 2014 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */

namespace HAB\Solr\Facet;

/**
 * An abstract base class for facet adapters.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright Copyright (c) 2014 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
abstract class AbstractFacetAdapter implements FacetAdapterInterface
{
    /**
     * The facet value container.
     *
     * @var Value\Container\ContainerInterface
     */
    private $container;

    /**
     * Facet name.
     *
     * @var string
     */
    private $name;

    /**
     * Facet label, if any.
     *
     * @var string
     */
    private $label;

    /**
     * The request value mapper.
     *
     * @var ValueMapper\RequestValueMapperInterface
     */
    private $mapper;

    /**
     * Container sort function.
     *
     * @var callable
     */
    private $sort;

    /**
     * Application state.
     *
     * @var array
     */
    private $state;

    /**
     * Set sort function.
     *
     * The sort function is called with the facet value container as
     * sole argument.
     *
     * @param  callable $sort
     * @return void
     */
    public function setSortFunction ($sort)
    {
        $this->sort = $sort;
    }

    /**
     * Set facet label.
     *
     * @param  string $label
     * @return void
     */
    public function setLabel ($label)
    {
        $this->label = $label;
    }

    /**
     * Return facet label.
     *
     * @return string|null
     */
    public function getLabel ()
    {
        return $this->label ?: $this->getName();
    }

    /**
     * Return facet name.
     *
     * @return string
     */
    public function getName ()
    {
        if ($this->name == '') {
            $this->setName(spl_object_hash($this));
        }
        return $this->name;
    }

    /**
     * Set the facet name.
     *
     * @param  string $name
     * @return void
     */
    public function setName ($name)
    {
        $this->name = $name;
    }

    /**
     * Return facet value container.
     *
     * @return Value\Container\ContainerInterface
     */
    public function getFacetValueContainer ()
    {
        if ($this->container === null) {
            $this->setFacetValueContainer(new Value\Container\FacetValueList());
        }
        if ($this->sort !== null) {
            call_user_func($this->sort, $this->container);
        }
        return $this->container;
    }

    /**
     * Set facet value container.
     *
     * @param  Value\Container\ContainerInterface $container
     * @return void
     */
    public function setFacetValueContainer (Value\Container\ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function getRequestValueMapper ()
    {
        if ($this->mapper === null) {
            $this->setRequestValueMapper(new ValueMapper\RequestValueIdentityMapper());
        }
        return $this->mapper;
    }

    /**
     * Set the request value mapper.
     *
     * @param  ValueMapper\RequestValueMapperInterface $mapper
     * @return void
     */
    public function setRequestValueMapper (ValueMapper\RequestValueMapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }


    /**
     * {@inheritDoc}
     */
    public function setComponentState (array $state)
    {
        if (array_key_exists('f', $state)) {
            $params = $state['f'];
            if ($params && is_array($params) && isset($params[$this->getName()])) {
                $this->setFilterValues($params[$this->getName()]);
            }
        }
        $this->state = $state;
    }

    public function getGlobalComponentState ()
    {
        if ($this->state) {
            return $this->state;
        }
        return array();
    }

    /**
     * {@inheritDoc}
     */
    public function getComponentState ()
    {
        return array('f' => array($this->getName() => $this->getFilterValues()));
    }

    /**
     * Set filter values from request.
     *
     * @param  mixed $values
     * @return void
     */
    abstract public function setFilterValues ($values);

    /**
     * Return filter values for request.
     *
     * @return mixed
     */
    abstract public function getFilterValues ();
}
