<?php

namespace nodespark\DESConnector;

use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\ElasticsearchException;

/**
 * Class Client
 */
class Client implements ClientInterface {

  const CLUSTER_STATUS_GREEN = 'green';
  const CLUSTER_STATUS_YELLOW = 'yellow';
  const CLUSTER_STATUS_RED = 'red';

  /**
   * @var Client
   */
  protected $proxy_client;

  /**
   * ClientConnector constructor.
   *
   * @param Client $client
   */
  public function __construct($params) {
    $this->initClient($params);
  }

  /**
   * {@inheritdoc}
   */
  public function getClusterStatus() {
    try {
      $health = $this->client->cluster()->health();
      return $health['status'];
    } catch (ElasticsearchException $e) {
      return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function isClusterOk() {
    try {
      $health = $this->client->cluster()->health();
      if (in_array(
        $health['status'],
        [self::CLUSTER_STATUS_GREEN, self::CLUSTER_STATUS_YELLOW]
      )) {
        $status = TRUE;
      }
      else {
        $status = FALSE;
      }
    } catch (ElasticsearchException $e) {
      $status = FALSE;
    }
    return $status;
  }

  /**
   * {@inheritdoc}
   */
  public function getClusterInfo() {
    $result = [
      'state' => NULL,
      'health' => NULL,
      'stats' => NULL,
    ];

    try {
      $result['state'] = $this->client->cluster()->State();
    } catch (ElasticsearchException $e) {
      drupal_set_message($e->getMessage(), 'error');
    }

    try {
      $result['health'] = $this->client->cluster()->Health();
    } catch (ElasticsearchException $e) {
      drupal_set_message($e->getMessage(), 'error');
    }

    try {
      $result['stats'] = $this->client->cluster()->Stats();
    } catch (ElasticsearchException $e) {
      drupal_set_message($e->getMessage(), 'error');
    }

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getNodesProperties() {
    $result = FALSE;
    try {
      $result['stats'] = $this->client->nodes()->stats();
      $result['info'] = $this->client->nodes()->info();
    } catch (ElasticsearchException $e) {
      drupal_set_message($e->getMessage(), 'error');
    }

    return $result;
  }

  /**
   * Initialize the real Elasticsearch client.
   *
   * @param $params
   *   The client initializer.
   *
   * @return \Elasticsearch\Client
   */
  private function initClient($params) {
    $conn_params = array();
    if (isset($params['curl'])) {
      $conn_params['client']['curl'] = $params['curl'];
    }

    $builder = ClientBuilder::create();
    $builder->setHosts($params['hosts']);

    $this->proxy_client = $builder->build();
  }
}
