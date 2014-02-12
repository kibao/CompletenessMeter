<?php

/*
 * This file is part of the Completeness Mater package.
 *
 * (c) Przemysław Piechota
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kibao\CompletenessMeter\Operator;

use Kibao\CompletenessMeter\CompletenessSummary;
use Kibao\CompletenessMeter\Operator;
use IteratorAggregate;

class Average extends Operator
{
    /**
     * @var Operator
     */
    protected $operator;

    public function __construct($options = null)
    {
        if (isset($options['operator']) && !($options['operator'] instanceof Operator)) {
            throw new \Exception(sprintf(
                'The %s operator requires the "completenessMeter" be instance of Operator.',
                get_class($this)
            ));
        }
        parent::__construct($options);
    }

    public function evaluate($data)
    {
        if (!is_array($data) && !$data instanceof IteratorAggregate) {
            throw new \InvalidArgumentException(sprintf(
                'The %s operator requires the evaluated value be array or instance of IteratorAggregate.',
                get_class($this)
            ));
        }

        $summary = new CompletenessSummary($this, $data, function (Average $operator, array $summaries) {
            if (!count($summaries)) {
                return 0;
            }

            $completenessValues = array();
            foreach ($summaries as $s) {
                $completenessValues[] = $s->getCompletenessScore();
            }
            $avg = array_sum($completenessValues) / count($completenessValues);

            return (int) floor($avg / $operator->getOperator()->getMaxScore() * $operator->getWeight());

        });

        foreach ($data as $key => $value) {
            $summary->add($key, $this->operator->evaluate($value));
        }

        return $summary;
    }

    public function getMaxScore()
    {
        return $this->weight;
    }

    public function getRequiredOptions()
    {
        return array_merge(parent::getRequiredOptions(), array('operator'));
    }

    public function getOperator()
    {
        return $this->operator;
    }
}
