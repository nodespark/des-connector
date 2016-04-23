<?php

namespace nodespark\DESConnector;

/**
 * Class ClientFactory
 */
class ClientFactory {

  /**
   * Build an instance of the elastic search client
   *
   * @param array $params
   *   The client initializer parameters.
   *
   * @return \nodespark\DESConnector\ClientInterface
   */
  public static function create(array $params) {
    return new Client($params);
  }

}
