<?php

namespace nodespark\DESConnector\Elasticsearch\Aggregations;

/**
 * Class Aggregation
 * @package nodespark\DESConnector\Elasticsearch\Aggregations
 */
abstract class Aggregation implements AggregationInterface {
  /**
   * @var string
   */
  protected $name;

  /**
   * @var string
   */
  protected $fieldName;

  /**
   * @var string
   */
  protected $type;

  /**
   * @var bool
   */
  protected $global = FALSE;

  /**
   * @var array
   */
  protected $response;

  /**
   * Metric constructor.
   *
   * @param string $aggrName
   *   The aggregation name.
   * @param string $aggrFieldName
   *   The aggregation field name.
   */
  public function __construct($aggrName, $aggrFieldName, $aggrType) {
    $this->name = $aggrName;
    $this->fieldName = $aggrFieldName;
    $this->type = $aggrType;
  }

  /**
   * @param bool $isGlobal
   */
  public function setGlobalScope($isGlobal) {
    $this->global = $isGlobal;
  }

  public function getGlobalName() {
    return $this->name . '_global';
  }

  /**
   * Construct the aggregation body needed for Elasticsearch.
   */
  public function constructAggregation() {
    $aggregation = array(
      $this->name => array(
        $this->type => array(
          'field' => $this->fieldName,
        )
      )
    );

    if ($this->global) {
     $aggregation[$this->getGlobalName()] = array(
       'global' => array(),
       Aggregations::AGGS_STRING => $aggregation,
     );
    }

    return $aggregation;
  }

  /**
   * @return string
   *   The aggregation name.
   */
  public function getName() {
    return $this->name;
  }

  public function setResponse($response) {

  }

  public function parseResponse() {

  }
}
