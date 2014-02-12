<?php

/*
 * This file is part of the Completeness Mater package.
 *
 * (c) Przemysław Piechota
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kibao\CompletenessMeter;

class CompletenessSummary implements \ArrayAccess
{
    /**
     * Operator for which summary is
     * @var Operator
     */
    protected $operator;
    /**
     * @var integer
     */
    protected $completenessScore = null;
    /**
     * Subject for which summary is
     * @var mixed
     */
    protected $subject;
    protected $summaries = array();
    protected $failedChecks = array();
    protected $passedChecks = array();
    protected $calculated = false;
    protected $calculateClosure = null;
    protected $passed = null;
    protected $isPassedClosure = null;

    public function __construct(Operator $operator, $subject, \Closure $calculateClosure = null, \Closure $isPassedClosure = null)
    {
        $this->operator = $operator;
        $this->subject = $subject;
        $this->calculateClosure = $calculateClosure;
        $this->isPassedClosure = $isPassedClosure;
    }

    public function getCompletenessScore()
    {
        if (!$this->calculated) {
            $this->calculate();
        }

        return $this->completenessScore;
    }

    public function getMaxScore()
    {
        return $this->operator->getMaxScore();
    }

    public function getPercentComplete()
    {
        if (!$this->calculated) {
            $this->calculate();
        }

        return (int) floor($this->completenessScore / $this->getMaxScore() * 100);
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function calculate()
    {
        if ($this->calculated) {
            throw new \LogicException ('Completeness Summary can only be calculated once');
        }

        if ($this->calculateClosure == null) {
            $completenessScore = 0;
            foreach ($this->summaries as $summary) {
                $completenessScore += $summary->getCompletenessScore();
            }
        } else {
            $closure = $this->calculateClosure;
            $completenessScore = $closure($this->operator, $this->summaries);
        }

        $this->completenessScore = $completenessScore;
        $this->calculated = true;
    }

    public function isPassed()
    {
        if (null !== $this->passed) {
            return $this->passed;
        }

        if ($this->isPassedClosure == null) {
            $this->passed = false;
            foreach ($this->summaries as $summary) {
                if ($summary->isPassed()) {
                    $this->passed = true;
                } else {
                    $this->passed = false;

                    return $this->passed;
                }
            }
        } else {
            $closure = $this->isPassedClosure;
            $this->passed = $closure($this, $this->operator);
        }

        return $this->passed;
    }

    public function add($key, CompletenessSummary $summary)
    {
        if (array_key_exists($key, $this->summaries)) {
            throw new \InvalidArgumentException(sprintf('The "%s" key already exists.', $key));
        }
        $this->summaries[$key] = $summary;
        if ($summary->isPassed()) {
            $this->passedChecks[] = $key;
        } else {
            $this->failedChecks[] = $key;
        }
    }

    public function has($key)
    {
        return array_key_exists($key, $this->summaries);
    }

    public function get($key)
    {
        if (!array_key_exists($key, $this->summaries)) {
            throw new \InvalidArgumentException(sprintf('The "%s" key doesn\'t exists.', $key));
        }

        return $this->summaries[$key];
    }

    public function getAll()
    {
        return $this->summaries;
    }

    public function getPassedChecks()
    {
        return $this->passedChecks;
    }

    public function getFailedChecks()
    {
        return $this->failedChecks;
    }

    /**
     * Returns whether the given child exists (implements \ArrayAccess).
     *
     * @param string $name The child name
     *
     * @return Boolean Whether the child summary exists
     */
    public function offsetExists($name)
    {
        return $this->has($name);
    }

    /**
     * Returns a child by name (implements \ArrayAccess).
     *
     * @param string $name The child name
     *
     * @return CompletenessSummary The child summary
     */
    public function offsetGet($name)
    {
        return $this->get($name);
    }

    /**
     * Implements \ArrayAccess
     * @throws \LogicException always as setting a summary by name is not allowed.
     */
    public function offsetSet($offset, $value)
    {
        throw new \LogicException('Not supported');
    }

    /**
     * Implements \ArrayAccess
     * @throws \LogicException always as unsetting a summary by name is not allowed.
     */
    public function offsetUnset($offset)
    {
        throw new \LogicException('Not supported');
    }
}
