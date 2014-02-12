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

class Exist extends Operator
{
    /**
     * @param $data
     * @return \Kibao\CompletenessMeter\CompletenessSummary
     */
    public function evaluate($data)
    {
        $passed = $this->exists($data);
        $score = $passed ? $this->weight : 0;

        $summary = new CompletenessSummary($this, array($data), function (Operator $operator, array $summaries) use ($score) {
            return $score;
        }, function (CompletenessSummary $summary) use ($passed) {
            return $passed;
        });

        return $summary;
    }

    public function getMaxScore()
    {
        return $this->weight;
    }

    protected function exists($value)
    {
        return $value !== null && (is_string($value) ? trim($value) !== '' : true);
    }
}
