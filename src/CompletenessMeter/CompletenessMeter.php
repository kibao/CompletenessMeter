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

class CompletenessMeter extends Operator
{
    protected $checks = array();

    public function add($key, Operator $operator)
    {
        if (array_key_exists($key, $this->checks)) {
            throw new \InvalidArgumentException(sprintf('The "%s" key already exists.', $key));
        }
        $this->checks[$key] = $operator;

        return $this;
    }

    /**
     * Returns completeness in percentages
     *
     * @param $data
     * @return \Kibao\CompletenessMeter\CompletenessSummary
     * @throws \InvalidArgumentException
     */
    public function evaluate($data)
    {
        if (!is_array($data) && !is_object($data)) {
            throw new \InvalidArgumentException('Data passed to evaluate method must be an array or object');
        }

        $summary = new CompletenessSummary($this, $data);

        foreach ($this->checks as $name => $operator) {
            $value = $this->getValue($data, $name);
            $propertySummary = $operator->evaluate($value);

            $summary->add($name, $propertySummary);
        }

        return $summary;
    }

    /**
     * Returns max possible score
     *
     * @return int
     */
    public function getMaxScore()
    {
        $maxCompletenessScore = 0;

        foreach ($this->checks as $operator) {
            $maxCompletenessScore += $operator->getMaxScore();
        }

        return $maxCompletenessScore;
    }

    /**
     * @param $data
     * @param $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    private function getValue($data, $name)
    {
        if (is_array($data)) {
            if (!array_key_exists($name, $data)) {
                throw new \InvalidArgumentException(sprintf('The "%s" key is missing.', $name));
            }

            return $data[$name];
        } else {
            return $this->getPropertyValue($data, $name);
        }
    }

    private function getPropertyValue($object, $property)
    {
        $className = get_class($object);
        $getMethod = 'get' . ucfirst($property);
        $isMethod = 'is' . ucfirst($property);

        if (property_exists($object, $property)) {
            $class = new \ReflectionClass($object);
            while (!$class->hasProperty($property)) {
                $class = $class->getParentClass();
            }

            $member = new \ReflectionProperty($class->getName(), $property);
            $member->setAccessible(true);

            return $member->getValue($object);
        }

        if (method_exists($className, $getMethod)) {
            $methodName = $getMethod;
        } elseif (method_exists($className, $isMethod)) {
            $methodName = $isMethod;
        } else {
            throw new \InvalidArgumentException(sprintf(
                'Neither property %s nor method %s nor %s exists in class %s',
                $property,
                $getMethod,
                $isMethod,
                $className
            ));
        }
        $method = new \ReflectionMethod($object, $methodName);

        return $method->invoke($object);
    }

}
