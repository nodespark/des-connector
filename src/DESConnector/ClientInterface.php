<?php

namespace nodespark\DESConnector;

/**
 * The Client interface with the required functions needed from
 * Elasticsearch Connector module.
 */
interface ClientInterface {
  public function info();
  public function getClusterStatus();
  public function isClusterOk();
  public function getClusterInfo();
  public function getNodesProperties();
  public function getIndicesStats();
}
