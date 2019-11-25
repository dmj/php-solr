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

namespace HAB\Solr\Facet\LabelFactory;

/**
 * Obtain a label by performing a search/replace operation on the value.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright Copyright (c) 2013-2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class RegExpLabelFactory implements LabelFactoryInterface
{
    /**
     * Pattern.
     *
     * @var string
     */
    private $pattern;

    /**
     * Replace template.
     *
     * @var string
     */
    private $template;

    /**
     * Constructor.
     *
     * @param  string $pattern
     * @param  string $template
     * @return void
     */
    public function __construct ($pattern, $template)
    {
        $this->pattern  = $pattern;
        $this->template = $template;
    }

    /**
     * {@inheritDoc}
     */
    public function createLabel ($value)
    {
        if (preg_match($this->pattern, $value)) {
            return (string)preg_replace($this->pattern, $this->template, $value);
        }
        return $value;
    }
}
