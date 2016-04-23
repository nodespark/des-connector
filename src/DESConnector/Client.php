<?php

namespace nodespark\DESConnector;

use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\ElasticsearchException;

/**
 * TODO: Remove the drupal related functions.
 */

/**
 * Class Client
 */
class Client implements ClientInterface {

  const CLUSTER_STATUS_GREEN = 'green';
  const CLUSTER_STATUS_YELLOW = 'yellow';
  const CLUSTER_STATUS_RED = 'red';

  /**
   * @var \Elasticsearch\Client
   */
  protected $proxy_client;

  protected $params;

  /**
   * Client constructor.
   *
   * @param array $params
   *   The params that should initialize the client.
   */
  public function __construct($params) {
    $this->params = $params;
    $this->initClient($this->params);
  }

  /**
   * Magic method __call().
   * If the method is not found, search if the method exists in the proxy.
   * If exists, call it.
   *
   * @param $name
   * @param $arguments
   */
  public function __call($name, $arguments) {
    if(method_exists($this->proxy_client, $name)) {
      return call_user_func_array(array($this->proxy_client, $name), $arguments);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getClusterStatus() {
    try {
      $health = $this->proxy_client->cluster()->health();
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
      $health = $this->proxy_client->cluster()->health();
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

  public function info() {
    $info = $this->proxy_client->info();
    // Compatible with D7 version.
    $info['status'] = 200;
    return $info;
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
      $result['info'] = $this->proxy_client->info();
    }
    catch (ElasticsearchException $e) {
      throw $e;
    }

    try {
      $result['state'] = $this->proxy_client->cluster()->State();
    } catch (ElasticsearchException $e) {
      drupal_set_message($e->getMessage(), 'error');
    }

    try {
      $result['health'] = $this->proxy_client->cluster()->Health();
    } catch (ElasticsearchException $e) {
      drupal_set_message($e->getMessage(), 'error');
    }

    try {
      $result['stats'] = $this->proxy_client->cluster()->Stats();
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
      $result['stats'] = $this->proxy_client->nodes()->stats();
      $result['info'] = $this->proxy_client->nodes()->info();
    } catch (ElasticsearchException $e) {
      drupal_set_message($e->getMessage(), 'error');
    }

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getIndicesStats() {
    return $this->proxy_client->indices()->stats();
  }
  
  /**
   * Initialize the real Elasticsearch client.
   *
   * @param $params
   *   The client initializer.
   *
   * @return \Elasticsearch\Client
   */
  protected function initClient($params) {
    $conn_params = array();
    if (isset($params['curl'])) {
      $conn_params['client']['curl'] = $params['curl'];
    }

    $builder = ClientBuilder::create();
    $params = $this->handleUrls($params);
    $builder->setHosts($params['hosts']);

    $this->proxy_client = $builder->build();
  }

  /**
   * Handle the URLs specifics like authentication.
   *
   * @param array $params
   *   The client initializer params.
   *
   * @return array
   *   Reworked params if needed.
   */
  private function handleUrls($params) {
    if (isset($params['auth'])) {
      foreach ($params['hosts'] as $key => $url) {
        $url_parsed = parse_url($url);
        if ($url_parsed !== FALSE) {
          $url_parsed['user'] = $params['auth'][$url]['username'];
          $url_parsed['pass'] = $params['auth'][$url]['password'];
          $params['hosts'][$key] =
            ((isset($url_parsed['scheme'])) ? $url_parsed['scheme'] . '://' : '')
            .((isset($url_parsed['user'])) ? $url_parsed['user'] . ((isset($url_parsed['pass'])) ? ':' . $url_parsed['pass'] : '') .'@' : '')
            .((isset($url_parsed['host'])) ? $url_parsed['host'] : '')
            .((isset($url_parsed['port'])) ? ':' . $url_parsed['port'] : '')
            .((isset($url_parsed['path'])) ? $url_parsed['path'] : '')
            .((isset($url_parsed['query'])) ? '?' . $url_parsed['query'] : '')
            .((isset($url_parsed['fragment'])) ? '#' . $url_parsed['fragment'] : '');
        }
      }
    }

    return $params;
  }
}
