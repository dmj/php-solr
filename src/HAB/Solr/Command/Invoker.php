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
 * @copyright (c) 2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */

namespace HAB\Solr\Command;

use HAB\Solr\ParamBag;

use GuzzleHttp\Client;

/**
 * Solr command invoker.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class Invoker
{
    /**
     * Base Url.
     *
     * @var string
     */
    private $baseUrl;

    /**
     * Client.
     *
     * @var Client
     */
    private $client;

    /**
     * Parameter provider.
     *
     * @var ParamBag
     */
    private $parameters;

    public function __construct ($baseUrl, Client $client)
    {
        $this->baseUrl = $baseUrl;
        $this->client = $client;
    }

    /**
     * Invoke command.
     *
     * @param  CommandInterface $command
     * @param  ParamBag         $parameters
     * @return mixed
     */
    public function invoke (CommandInterface $command, ParamBag $parameters = null)
    {
        $params = $this->getParameters();
        if ($parameters) {
            $params->mergeWith($parameters);
        }

        $query = $params->mergeWith($command->getParameters())->request();
        $url = sprintf('%s/%s?%s', $this->baseUrl, $command->getHandler(), implode('&', $query));

        $response = $this->getClient()->request('GET', $url);

        $command->setResponse($response->getBody());
        return $command->getResult();
    }

    /**
     * Return client.
     *
     * @return Client
     */
    public function getClient ()
    {
        return $this->client;
    }

    /**
     * Return parameters.
     *
     * @return ParamBag
     */
    public function getParameters ()
    {
        if (is_null($this->parameters)) {
            $this->setParameters(new ParamBag());
        }
        return $this->parameters;
    }

    /**
     * Set parameters.
     *
     * @param  ParamBag $parameters
     * @return
     */
    public function setParameters (ParamBag $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Encode key-value-pair.
     *
     * @param  string $name
     * @param  string $value
     * @return string
     */
    private static function encode ($name, $value)
    {
        return sprintf('%s=%s', urlencode($name), urlencode($value));
    }
}