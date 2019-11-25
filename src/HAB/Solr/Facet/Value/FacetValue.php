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

namespace HAB\Solr\Facet\Value;

use Collator;

/**
 * A facet value.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright Copyright (c) 2014-2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class FacetValue
{
    /**
     * Collator instance.
     *
     * @var Collator
     */
    private static $collator;

    /**
     * Compare two facet labels for sort.
     *
     * @see http://www.php.net/manual/en/function.usort.php
     *
     * @param  FacetValue $v1
     * @param  FacetValue $v2
     * @return integer
     */
    public static function compareByLabel (FacetValue $v1, FacetValue $v2)
    {
        $collator = self::getCollator();
        return $collator->compare($v1->getLabel(), $v2->getLabel());
    }

    /**
     * Compare two facet counts for sort.
     *
     * @see http://www.php.net/manual/en/function.usort.php
     *
     * @param  FacetValue $v1
     * @param  FacetValue $v2
     * @return integer
     */
    public static function compareByCount (FacetValue $v1, FacetValue $v2)
    {
        $c1 = $v1->getCount();
        $c2 = $v2->getCount();
        if ($v1 == $v2) return 0;
        return ($v1 > $v2) ? 1 : -1;
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
     * Facet value.
     *
     * @var string
     */
    private $value;

    /**
     * Facet value count.
     *
     * A value of boolean false indicates an unknown facet value
     * count.
     *
     * @var integer|false
     */
    private $count;

    /**
     * Is the currently value selected?
     *
     * @var boolean
     */
    private $selected;

    /**
     * Facet value label.
     *
     * @var string
     */
    private $label;

    /**
     * Query parameters.
     *
     * @var mixed
     */
    private $query;

    /**
     * Constructor.
     *
     * @param  string        $value
     * @param  integer|false $count
     * @param  boolean       $selected
     * @return void
     */
    public function __construct ($value, $count, $selected)
    {
        $this->setValue($value);
        $this->setCount($count);
        $this->setSelected($selected);
    }

    /**
     * Set the facet value.
     *
     * @param  string $value
     * @return void
     */
    public function setValue ($value)
    {
        $this->value = $value;
    }

    /**
     * Set the facet value count.
     *
     * @param  integer|false $count
     * @return void
     */
    public function setCount ($count)
    {
        $this->count = ($count === false ? $count : (int)$count);
    }

    /**
     * Set selected marker.
     *
     * @param  boolean $selected
     * @return void
     */
    public function setSelected ($selected)
    {
        $this->selected = (boolean)$selected;
    }

    /**
     * Set facet value label.
     *
     * @param  string $label
     * @return void
     */
    public function setLabel ($label)
    {
        $this->label = $label;
    }

    /**
     * Return facet value.
     *
     * @return string
     */
    public function getValue ()
    {
        return $this->value;
    }

    /**
     * Return facet value count.
     *
     * @return integer|false
     */
    public function getCount ()
    {
        return $this->count;
    }

    /**
     * Is the facet value selected?
     *
     * @return boolean
     */
    public function isSelected ()
    {
        return $this->selected;
    }

    /**
     * Return facet value label.
     *
     * Returns the facet value is no label is defined.
     *
     * @return string
     */
    public function getLabel ()
    {
        return $this->label ?: $this->getValue();
    }

    /**
     * Return query parameters.
     *
     * @return mixed|null
     */
    public function getQuery ()
    {
        return $this->query;
    }

    /**
     * Set query parameters.
     *
     * @param  mixed $query
     * @return void
     */
    public function setQuery ($query)
    {
        $this->query = $query;
    }
}
