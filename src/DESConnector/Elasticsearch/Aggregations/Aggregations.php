<?php

namespace nodespark\DESConnector\Elasticsearch\Aggregations;

// TODO: We need to have an interface.
// TODO: Singleton definition of that array if needed!

/**
 * TODO:
 * Should generate the following output at the end:
 *

"aggregations" : {
"<aggregation_name>" : {
"<aggregation_type>" : {
<aggregation_body>
}
[,"meta" : {  [<meta_data_body>] } ]?
[,"aggregations" : { [<sub_aggregation>]+ } ]?
}
[,"<aggregation_name_2>" : { ... } ]*
}

 * Class Aggregations
 * @package nodespark\DESConnector\Elasticsearch\Aggregations
 */
class Aggregations {
  const AGGS_STRING = 'aggs';

  /**
   * @var Aggregations The reference to *Singleton* instance of this class
   */
  private static $instance;

  /**
   * Returns the *Singleton* instance of this class.
   *
   * @return Aggregations The *Singleton* instance.
   */
  public static function getInstance($id = 'default')
  {
    if (null === static::$instance[$id]) {
      static::$instance[$id] = new static();
    }

    return static::$instance[$id];
  }

  /**
   * Protected constructor to prevent creating a new instance of the
   * *Singleton* via the `new` operator from outside of this class.
   */
  protected function __construct() {}

  /**
   * Private clone method to prevent cloning of the instance of the
   * *Singleton* instance.
   *
   * @return void
   */
  private function __clone() {}

  /**
   * Private unserialize method to prevent unserializing of the *Singleton*
   * instance.
   *
   * @return void
   */
  private function __wakeup() {}

  /**
   * @var array
   *   Array of Aggregation objects.
   */
  protected $aggregations = array();

  public function setAggregation(AggregationInterface $aggregation) {
    $this->aggregations[$aggregation->getName()] = $aggregation;
  }

  public function constructAggregation() {
    $aggregationArray = array();
    foreach ($this->aggregations as $name => $aggregationObj) {
      $aggsArray = $aggregationObj->constructAggregation();
      $aggregationArray[$name] = $aggsArray[$name];
    }

    if (!empty($aggregationArray)) {
      return array(Aggregations::AGGS_STRING => $aggregationArray);
    }
    else {
      return $aggregationArray;
    }
  }

  public function applyAggregationsToParams(&$params) {
    $aggregationParams = $this->constructAggregation();
    if (!empty($aggregationParams)) {
      $params['body'][static::AGGS_STRING] =
        $aggregationParams[static::AGGS_STRING];
    }
  }

  public function setAggregationResponse($response) {

  }

}
