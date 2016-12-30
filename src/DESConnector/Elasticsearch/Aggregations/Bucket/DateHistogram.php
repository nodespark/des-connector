<?php

namespace nodespark\DESConnector\Elasticsearch\Aggregations\Bucket;

/**
 * Class DateHistogram.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-datehistogram-aggregation.html
 * @package nodespark\DESConnector\Elasticsearch\Aggregations\Bucket
 */
class DateHistogram extends Bucket {

    const TYPE = 'date_histogram';

    /**
     * FIXED_INTERVAL_TYPES
     */
    const FIXED_INTERVAL_TYPES = array(
        'year' => 'year',
        'quarter' => 'quarter',
        'month' => 'month',
        'week' => 'week',
        'day' => 'day',
        'hour' => 'hour',
        'minute' => 'minute',
        'second' => 'second',
    );

    /**
     * FRACTIONAL_INTERVALS
     */
    const FRACTIONAL_INTERVALS = array(
        'Y' => 'Y',
        'M' => 'M',
        'w' => 'w',
        'd' => 'd',
        'h' => 'h',
        'm' => 'm',
        's' => 's',
        'ms' => 'ms',
    );

    protected $interval;
    protected $format;

    /**
     * @param $interval
     */
    public function setInterval($interval) {
        // TODO: Validate the interval before set!
        $this->interval = $interval;
    }

    /**
     * @param $format
     */
    public function setFormat($format) {
        $this->format = $format;
    }

    /**
     * DateHistogram constructor.
     * @param string $aggrName
     * @param string $aggrFieldName
     */
    public function __construct($aggrName, $aggrFieldName, $interval) {
        parent::__construct($aggrName, $aggrFieldName, self::TYPE);
        $this->setInterval($interval);
    }

    /**
     * @inheritdoc
     */
    public function constructAggregation() {
        $aggregation = parent::constructAggregation();

        // Set the additional parameters if needed.
        if (isset($this->format)) {
            $aggregation[$this->name][self::TYPE]['format'] = $this->format;
        }

        if (isset($this->interval)) {
            $aggregation[$this->name][self::TYPE]['interval'] = $this->interval;
        }

        return $aggregation;
    }

}
