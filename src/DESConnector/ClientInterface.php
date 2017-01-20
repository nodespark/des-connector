<?php

namespace nodespark\DESConnector;

use nodespark\DESConnector\Elasticsearch\Response\SearchResponseInterface;

/**
 * The Client interface with the required functions needed from
 * Elasticsearch Connector module.
 *
 * TODO: Describe all the methods with comments.
 *
 */
interface ClientInterface
{
    public function info();

    public function getClusterStatus();

    public function isClusterOk();

    public function getClusterInfo();

    public function getNodesProperties();

    public function getIndicesStats();

    public function CheckResponseAck($response);

    public function getServerVersion();

    public function getInstalledPlugins();

    public function checkIfPluginExists($plugin_name);

    /**
     * Get the SearchResponseInterface handler to parse the search response.
     *
     * @return SearchResponseInterface
     */
    public function getSearchResponse();

    /**
     * @param array $searchResponse
     * @return SearchResponseInterface
     */
    public function setSearchResponse(array $searchResponse);

    /**
     * @return \Elasticsearch\Namespaces\IndicesNamespace
     */
    public function indices();

    /**
     * Return the aggregations object.
     *
     * @return \nodespark\DESConnector\Elasticsearch\Aggregations\AggregationsInterface
     */
    public function aggregations();

    /**
     * Search API to Elasticsearch.
     *
     * @param $params
     * @return SearchResponseInterface
     */
    public function search($params);
}
