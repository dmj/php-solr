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

namespace HAB\Solr\Facet\Impl;

use HAB\Solr\Facet\FacetImplInterface;
use HAB\Solr\Facet\Value\FacetValue;

use HAB\Solr\Response\Json\RecordCollection;

use Traversable;
use InvalidArgumentException;

/**
 * A simple SOLR field facet.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright Copyright (c) 2013-2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class FieldFacet implements FacetImplInterface
{
    /**
     * Field to facet on.
     *
     * @var string
     */
    private $field;

    /**
     * Selected values.
     *
     * @var string[]
     */
    private $selected;

    /**
     * Filter query operator.
     *
     * @var string
     */
    private $fqOperator;

    /**
     * Filter query tag, if any.
     *
     * @var string|null
     */
    private $fqTag;

    /**
     * Is multiselect enabled?
     *
     * @var boolean
     */
    private $multiselect;

    /**
     * Facet options.
     *
     * @var string[]
     */
    private $options;

    /**
     * Local parameters.
     *
     * @var LocalParams
     */
    private $localParams;

    /**
     * Facet counts.
     *
     * @var array
     */
    private $counts;

    /**
     * Constructor.
     *
     * @param  string $field
     * @param  array  $options
     * @return void
     */
    public function __construct ($field, array $options = array())
    {
        if (empty($field)) {
            throw new InvalidArgumentException('Facet field must not be empty');
        }
        $this->selected = array();
        $this->field    = $field;
        $this->setFacetOptions($options);
    }

    /**
     * {@inheritDoc}
     */
    public function getSearchParameters ()
    {
        $params = $this->getFacetOptions();
        $params['facet'] = 'true';
        $params['facet.field'] = sprintf("%s%s", (string)$this->getLocalParams(), $this->field);
        if ($fq = $this->getFilterQuery()) {
            $params['fq'] = $fq;
        }
        return $params;
    }

    /**
     * {@inheritDoc}
     */
    public function setRecordCollection (RecordCollection $response)
    {
        $facets = $response->getFacets();
        $fields = $facets->getFieldFacets();
        if (isset($fields[$this->field])) {
            $this->setFacetCounts($fields[$this->field]);
        }
    }

    /**
     * Set facet counts.
     *
     * The parameter is expected to be an array or Traversable that
     * maps index values to value counts.
     *
     * @param  array|Traversable $counts
     * @return void
     */
    public function setFacetCounts ($counts)
    {
        $this->counts = array();
        foreach ($counts as $value => $count) {
            $this->counts []= new FacetValue($value, $count, $this->isSelected($value));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getFacetCounts ()
    {
        if ($this->counts === null) {
            $this->setFacetCounts(array());
        }
        return $this->counts;
    }

    /**
     * Return local params.
     *
     * @return LocalParams
     */
    public function getLocalParams ()
    {
        if (empty($this->localParams)) {
            $this->localParams = new LocalParams();
        }
        return $this->localParams;
    }

    /**
     * {@inheritDoc}
     */
    public function setSelected (array $selected)
    {
        $this->selected = $selected;
    }

    /**
     * Return selected values.
     *
     * @return string[]
     */
    public function getSelected ()
    {
        if ($this->selected === null) {
            $this->setSelected(array());
        }
        return $this->selected;
    }

    /**
     * Is the value selected?
     *
     * @param  string $value
     * @return boolean
     */
    public function isSelected ($value)
    {
        return in_array($value, $this->selected);
    }

    /**
     * Set boolean operator for filter query.
     *
     * @throws InvalidArgumentException Boolean operator neither 'or' nor 'and'
     *
     * @param  string $operator
     * @return void
     */
    public function setFilterQueryOperator ($operator)
    {
        $operator = trim(strtoupper($operator));
        if ($operator !== 'AND' && $operator !== 'OR') {
            throw new InvalidArgumentException(
                sprintf(
                    "Invalid filter query operator -- expected 'OR' or 'AND', got %s', $operator",
                    $operator
                )
            );
        }
        $this->fqOperator = $operator;
    }

    /**
     * Return filter query operator.
     *
     * @return string
     */
    public function getFilterQueryOperator ()
    {
        if (empty($this->fqOperator)) {
            $this->setFilterQueryOperator('AND');
        }
        return $this->fqOperator;
    }

    /**
     * Return filter query tag.
     *
     * @return string
     */
    public function getFilterQueryTag ()
    {
        if (empty($this->fqTag)) {
            $this->fqTag = sprintf('%s.%s', $this->field, spl_object_hash($this));
        }
        return $this->fqTag;
    }

    /**
     * Is multiselected enabled?
     *
     * @return boolean
     */
    public function isMultiselect ()
    {
        return $this->multiselect;
    }

    /**
     * Enable or disable multiselect.
     *
     * When multiselect is enabled that facet's filter query is
     * excluded from calculating the facet counts.
     *
     * @param  boolean $enable
     * @return void
     */
    public function enableMultiselect ($enable = true)
    {
        $local = $this->getLocalParams();
        if ($enable) {
            $local->set('ex', $this->getFilterQueryTag());
        } else {
            $local->remove('ex');
        }
        $this->multiselect = (boolean)$enable;
    }

    /**
     * Return the filter query.
     *
     * @return string
     */
    public function getFilterQuery ()
    {
        if (!empty($this->selected)) {
            $local = new LocalParams();
            $local->add('q.op', $this->getFilterQueryOperator());
            if ($this->isMultiselect()) {
                $local->add('tag', $this->getFilterQueryTag());
            }
            return sprintf('%s%s:(%s)', (string)$local, $this->field, implode(' ', array_map(array($this, 'escape'), $this->selected)));
        }
    }

    /**
     * Set facet options.
     *
     * Facet options are encoded using SOLR's field specific syntax
     * option, e.g. as f.FIELD.option.
     *
     * @param  string[] $options
     * @return void
     */
    public function setFacetOptions (array $options)
    {
        $this->options = array();
        $field = $this->field;
        foreach ($options as $key => $value) {
            $this->options["f.{$field}.{$key}"] = $value;
        }
    }

    /**
     * Return facet options.
     *
     * @return array
     */
    public function getFacetOptions ()
    {
        if ($this->options === null) {
            $this->setFacetOptions(array());
        }
        return $this->options;
    }

    /**
     * Escape values for filter query request.
     *
     * Encloses each value in double-quotes.
     *
     * @param  string $value
     * @return string
     */
    private function escape ($value)
    {
        return '"' . addcslashes($value, '"') . '"';
    }

    /**
     * {@inheritDoc}
     */
    public function __clone ()
    {
        if ($this->localParams !== null) {
            $this->localParams = clone($this->localParams);
        }
        $this->fqTag = null;
    }
}
