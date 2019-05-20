HAB Solr
==

This library implements facetted Solr search. It started as a spin-off of [VuFind's search component](https://github.com/vufind-org)
but simplified the communication with the Solr backend. It implements the [Command pattern](https://en.wikipedia.org/wiki/Command_pattern)
and provides customizable access to facets. The concept of generic *parameter providers* and *response consumers* makes it
easy to implemented access to Solr features not covered by this library.

HAB Solr is Copyright (c) 2016-2019 by Herzog August Bibliothek Wolfenbüttel and released under the terms of the GNU
General Public License v3 or higher.

Using the invoker
--

The [Invoker](src/HAB/Solr/Command/Search.php) reads the query parameters from the [Command](src/HAB/Solr/Command/Search.php), 
optionally merges them with static or dynamic default parameters, and communicates with the Solr backend using a
[Guzzle](http://guzzlephp.org) HTTP client.

The body of the Solr response is passed to [Command](src/HAB/Solr/Command/Search.php).

Using the search command
--

The [Search](src/HAB/Solr/Command/Search.php) command performs a search on a Solr query handler. 

The search query is represented by an ArrayObject and translated into a Solr search query by a
[QueryBuilder](src/HAB/Solr/Command/QueryBuilder.php). The default implementation performs a 1:1 mapping of the
ArrayObject's keys and values to Solr search parameters.

