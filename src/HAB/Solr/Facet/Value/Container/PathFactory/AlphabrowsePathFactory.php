<?php

/**
 * This file is part of HAB Solr NG.
 *
 * HAB Solr NG is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * HAB Solr NG is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HAB Solr NG.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */

namespace HAB\Solr\Facet\Value\Container\PathFactory;

use SplObjectStorage;
use Normalizer;

/**
 * Create a path into an alphabetical browse tree.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class AlphabrowsePathFactory implements PathFactoryInterface
{
    /**
     * Prefix, if any.
     *
     * @var string
     */
    private $prefix;

    /**
     * Prefix lenght.
     *
     * @var integer
     */
    private $prefixLength;

    /**
     * Cache.
     *
     * @var SplObjectStorage
     */
    private $cache;

    /**
     * Constructor.
     *
     * @param  string $prefix
     * @return void
     */
    public function __construct ($prefix = null)
    {
        if ($prefix) {
            $this->setPrefix($prefix);
        }
        $this->cache = new SplObjectStorage();
    }

    /**
     * Set prefix.
     *
     * @param  string $prefix
     * @return void
     */
    public function setPrefix ($prefix)
    {
        $this->prefix = $prefix;
        $this->prefixLength = strlen($prefix);
    }

    /**
     * {@inheritDoc}
     */
    public function encodesFacetValueTreePath (FacetValue $value)
    {
        if ($this->prefix and strpos($value->getValue(), $this->prefix) !== 0) {
            return false;
        }
        $this->cache->attach($value);
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function createFacetValueTreePath (FacetValue $value)
    {
        if (!$this->cache->contains($value) and !$this->encodesFacetValueTreePath($value)) {
            return array();
        }
        $normvalue = $this->prefix ? substr($value->getValue(), $this->prefixLength) : $value->getValue();
        if ($normvalue) {
            $segment = normalizer_normalize($normvalue, Normalizer::NFD);
            $nodeId = ord(strtoupper($segment[0]));
            if ($nodeId > 64 and $nodeId < 91) {
                return array(strtoupper($segment[0]), $normvalue);
            }
            if ($nodeId > 47 and $nodeId < 58) {
                return array('0-9');
            }
        }
        return array();
    }
}