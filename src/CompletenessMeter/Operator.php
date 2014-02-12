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

abstract class Operator
{
    protected $weight = 10;

    /**
     * Initializes the operator with options.
     *
     * You should pass an associative array. The keys should be the names of
     * existing properties in this class. The values should be the value for these
     * properties.
     *
     * Alternatively you can override the method getDefaultOption() to return the
     * name of an existing property. If no associative array is passed, this
     * property is set instead.
     *
     * You can force that certain options are set by overriding
     * getRequiredOptions() to return the names of these options. If any
     * option is not set here, an exception is thrown.
     *
     * @param mixed $options The options (as associative array)
     *                       or the weight for the default
     *                       option (any other type)
     *
     * @throws \Exception When you pass the names of non-existing
     *                    options
     * @throws \Exception When you don't pass any of the options
     *                    returned by getRequiredOptions()
     * @throws \Exception When you don't pass an associative
     *                    array, but getDefaultOption() returns
     *                    NULL
     */
    public function __construct($options = null)
    {
        $invalidOptions = array();
        $missingOptions = array_flip((array) $this->getRequiredOptions());

        if (is_array($options) && count($options) == 1 && isset($options['weight'])) {
            $options = $options['weight'];
        }

        if (is_array($options) && count($options) > 0 && is_string(key($options))) {
            foreach ($options as $option => $value) {
                if (property_exists($this, $option)) {
                    $this->$option = $value;
                    unset($missingOptions[$option]);
                } else {
                    $invalidOptions[] = $option;
                }
            }
        } elseif (null !== $options && !(is_array($options) && count($options) === 0)) {
            $option = $this->getDefaultOption();

            if (null === $option) {
                throw new \Exception(
                    sprintf('No default option is configured for operator %s', get_class($this))
                );
            }

            if (property_exists($this, $option)) {
                $this->$option = $options;
                unset($missingOptions[$option]);
            } else {
                $invalidOptions[] = $option;
            }
        }

        if (count($invalidOptions) > 0) {
            throw new \Exception(
                sprintf(
                    'The options "%s" do not exist in operator %s',
                    implode('", "', $invalidOptions),
                    get_class($this)
                )
            );
        }

        if (count($missingOptions) > 0) {
            throw new \Exception(
                sprintf(
                    'The options "%s" must be set for operator %s',
                    implode('", "', array_keys($missingOptions)),
                    get_class($this)
                )
            );
        }
    }

    /**
     * Returns the name of the default option
     *
     * Override this method to define a default option.
     *
     * @return string
     * @see __construct()
     */
    public function getDefaultOption()
    {
        return 'weight';
    }

    /**
     * Returns the name of the required options
     *
     * Override this method if you want to define required options.
     *
     * @return array
     * @see __construct()
     */
    public function getRequiredOptions()
    {
        return array();
    }

    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param $data
     * @return \Kibao\CompletenessMeter\CompletenessSummary
     */
    abstract public function evaluate($data);

    abstract public function getMaxScore();
}
