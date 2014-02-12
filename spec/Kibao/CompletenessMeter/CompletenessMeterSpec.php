<?php

/*
 * This file is part of the Completeness Mater package.
 *
 * (c) Przemysław Piechota
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Kibao\CompletenessMeter;

use Kibao\CompletenessMeter\CompletenessSummary;
use Kibao\CompletenessMeter\Operator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CompletenessMeterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Kibao\CompletenessMeter\CompletenessMeter');
    }

    function it_extends_Completeness_Meter_operator()
    {
        $this->shouldHaveType('Kibao\CompletenessMeter\Operator');
    }

    function its_evaluate_correct_for_array(Operator $operator, CompletenessSummary $summary)
    {
        $summary->getCompletenessScore()->willReturn(10);
        $summary->isPassed()->willReturn(true);
        $operator->evaluate(Argument::any())->willReturn($summary);
        $operator->getMaxScore()->willReturn(10);

        $this->add('key1', $operator);
        $this->evaluate(array('key1' => "value"))->getCompletenessScore()->shouldReturn(10);
        $this->evaluate(array('key1' => "value"))->getPercentComplete()->shouldReturn(100);
    }

    function its_evaluate_correct_for_array_with_failures(Operator $operator, CompletenessSummary $summary, Operator $operator2, CompletenessSummary $summary2)
    {
        $summary->getCompletenessScore()->willReturn(10);
        $summary->isPassed()->willReturn(true);
        $operator->getMaxScore()->willReturn(50);
        $operator->evaluate(Argument::exact('value'))->willReturn($summary);

        $summary2->getCompletenessScore()->willReturn(0);
        $summary2->isPassed()->willReturn(true);
        $operator2->getMaxScore()->willReturn(50);
        $operator2->evaluate(Argument::any())->willReturn($summary2);

        $this->add('key1', $operator);
        $this->add('key2', $operator2);
        $this->evaluate(array('key1' => "value", 'key2' => "10"))->getCompletenessScore()->shouldReturn(10);
    }

    function its_evaluate_correct_for_object(Operator $operator, CompletenessSummary $summary)
    {
        $summary->getCompletenessScore()->willReturn(10);
        $summary->isPassed()->willReturn(true);
        $operator->getMaxScore()->willReturn(10);
        $operator->evaluate(Argument::exact("value"))->willReturn($summary);

        $object = new WithProperties('value', '10');

        $this->add('key1', $operator);
        $this->evaluate($object)->getCompletenessScore()->shouldReturn(10);
        $this->evaluate(array('key1' => "value"))->getPercentComplete()->shouldReturn(100);
    }

    function its_evaluate_correct_for_object_with_failures(Operator $operator, CompletenessSummary $summary, Operator $operator2, CompletenessSummary $summary2)
    {
        $summary->getCompletenessScore()->willReturn(10);
        $summary->isPassed()->willReturn(true);
        $operator->getMaxScore()->willReturn(50);
        $operator->evaluate(Argument::exact("value"))->willReturn($summary);

        $summary2->getCompletenessScore()->willReturn(0);
        $summary2->isPassed()->willReturn(true);
        $operator2->getMaxScore()->willReturn(50);
        $operator2->evaluate(Argument::any())->willReturn($summary2);

        $object = new WithProperties('value', '10');

        $this->add('key1', $operator);
        $this->add('key2', $operator2);
        $this->evaluate($object)->getCompletenessScore()->shouldReturn(10);
    }

    function its_evaluate_correct_for_with_methods_object(Operator $operator, CompletenessSummary $summary)
    {
        $summary->getCompletenessScore()->willReturn(10);
        $summary->isPassed()->willReturn(true);
        $operator->getMaxScore()->willReturn(10);
        $operator->evaluate(Argument::exact("value"))->willReturn($summary);

        $object = new WithMethods('value', '10');

        $this->add('key1', $operator);
        $this->evaluate($object)->getCompletenessScore()->shouldReturn(10);
    }

    function its_evaluate_correct_for_with_methods_object_with_failures(Operator $operator, CompletenessSummary $summary, Operator $operator2, CompletenessSummary $summary2)
    {
        $summary->getCompletenessScore()->willReturn(10);
        $summary->isPassed()->willReturn(true);
        $operator->getMaxScore()->willReturn(50);
        $operator->evaluate(Argument::exact("value"))->willReturn($summary);

        $summary2->getCompletenessScore()->willReturn(0);
        $summary2->isPassed()->willReturn(true);
        $operator2->getMaxScore()->willReturn(50);
        $operator2->evaluate(Argument::any())->willReturn($summary2);

        $object = new WithMethods('value', '10');

        $this->add('key1', $operator);
        $this->add('key2', $operator2);
        $this->evaluate($object)->getCompletenessScore()->shouldReturn(10);
    }
}

class WithProperties
{
    private $key1;
    protected $key2;

    function __construct($key1, $key2)
    {
        $this->key1 = $key1;
        $this->key2 = $key2;
    }
}

class WithMethods
{
    private $value1;
    protected $value2;

    function __construct($key1, $key2)
    {
        $this->value1 = $key1;
        $this->value2 = $key2;
    }

    function getKey1()
    {
        return $this->value1;
    }

    function getKey2()
    {
        return $this->value2;
    }
}
