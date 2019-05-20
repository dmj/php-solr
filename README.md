HAB Solr
==

This library implements facetted Solr search. It started as a spin-off of [https://github.com/vufind-org](VuFind's
search component) but simplified the communication with the Solr backend. It implements the
[https://en.wikipedia.org/wiki/Command_pattern](Command pattern) and provides customizable access to facets. The concept
of generic *parameter providers* and *response consumers* makes it easy to implemented access to Solr features not
covered by this library.

HAB Solr is Copyright (c) 2016-2019 by Herzog August Bibliothek Wolfenbüttel and released under the terms of the GNU
General Public License v3 or higher.
