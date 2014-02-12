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

class ExistCollection extends Exist
{
    protected $maxElements = 1;

    /**
     * @param $data
     * @return \Kibao\CompletenessMeter\CompletenessSummary
     * @throws \InvalidArgumentException
     */
    public function evaluate($data)
    {
        if (!is_array($data) && !$data instanceof IteratorAggregate) {
            throw new \InvalidArgumentException('Data passed to evaluate method must be an array or IteratorAggregate');
        }

        $summary = new CompletenessSummary($this, $data, function (ExistCollection $operator, array $summaries) {
            if (!count($summaries)) {
                return 0;
            }

            $count = 0;
            foreach ($summaries as $s) {
                if ($s->isPassed()) {
                    $count++;
                }
                if ($count === $operator->getMaxElements()) {
                    break;
                }
            }
            $count = $count < $operator->getMaxElements() ? $count : $operator->getMaxElements();

            return $operator->getWeight() * $count;

        });

        $count = 0;

        foreach ($data as $name => $v) {
            $subSummary = parent::evaluate($v);
            $summary->add($name, $subSummary);

            if ($subSummary->isPassed()) {
                $count++;
            }
            if ($count === $this->maxElements) {
                break;
            }
        }

        // Add failed checks
        if ($count < $this->maxElements - 1) {
            for ($i = $count; $i < $this->maxElements; $i++) {
                $s = new CompletenessSummary($this, array($data), function (Operator $operator, array $summaries) {
                    return 0;
                }, function (CompletenessSummary $summary) {
                    return 0;
                });

                $summary->add('failed_' . $i, $s);
            }
        }

        return $summary;
    }

    public function getMaxScore()
    {
        return $this->maxElements * $this->weight;
    }

    public function getMaxElements()
    {
        return $this->maxElements;
    }
}
