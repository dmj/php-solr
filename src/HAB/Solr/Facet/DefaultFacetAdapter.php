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

/**
 * Default implementation of a {@see FacetAdapterInterface}.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright Copyright (c) 2013-2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class DefaultFacetAdapter extends AbstractFacetAdapter
{
    /**
     * Decorated facet.
     *
     * @var FacetImplInterface
     */
    private $facet;

    /**
     * Facet value label factory.
     *
     * @var LabelFactory\LabelFactoryInterface
     */
    private $labelFactory;

    /**
     * Constructor.
     *
     * @param  FacetImplInterface $facet
     * @param  boolean        $sortByLabel
     * @return void
     */
    public function __construct (FacetImplInterface $facet, $sortByLabel = false)
    {
        $this->facet = $facet;
        if ($sortByLabel) {
            $this->setSortFunction(
                function (Value\Container\ContainerInterface $container) {
                    $container->sortByLabel();
                }
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setFilterValues ($values)
    {
        $this->facet->setSelected(
            $this->getRequestValueMapper()->mapFromRequest($values)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getFilterValues ()
    {
        return $this->getRequestValueMapper()->mapToRequest($this->facet->getSelected());
    }

    /**
     * {@inheritDoc}
     */
    public function setRecordCollection (RecordCollection $response)
    {
        $container = $this->getFacetValueContainer();
        $factory   = $this->getFacetValueLabelFactory();
        $selected  = $this->facet->getSelected();
        $values    = $this->facet->getFacetCounts();
        $mapper    = $this->getRequestValueMapper();
        $name      = $this->getName();
        foreach ($values as $value) {
            $query = $selected;
            if ($value->isSelected()) {
                foreach (array_keys($query, $value->getValue()) as $key) {
                    unset($query[$key]);
                }
            } else {
                $query []= $value->getValue();
            }
            $params = $this->getGlobalComponentState();
            if ($query) {
                $params['f'][$name] = $mapper->mapToRequest($query);
            } else {
                unset($params['f'][$name]);
            }
            $value->setQuery($params);
            $value->setLabel($factory->createLabel($value->getValue()));
            $container->addFacetValue($value);
        }
    }

    /**
     * Return facet value label factory.
     *
     * @return LabelFactory\LabelFactoryInterface
     */
    public function getFacetValueLabelFactory ()
    {
        if ($this->labelFactory === null) {
            $this->setFacetValueLabelFactory(new LabelFactory\DefaultLabelFactory());
        }
        return $this->labelFactory;
    }

    /**
     * Set facet value label factory.
     *
     * @param  LabelFactory\LabelFactoryInterface $factory
     * @return void
     */
    public function setFacetValueLabelFactory (LabelFactory\LabelFactoryInterface $factory)
    {
        $this->labelFactory = $factory;
    }
}
