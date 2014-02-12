<?php

/*
 * This file is part of the Completeness Mater package.
 *
 * (c) Przemysław Piechota
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Kibao\CompletenessMeter\Operator;

use PhpSpec\ObjectBehavior;

class ExistCollectionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Kibao\CompletenessMeter\Operator\ExistCollection');
    }

    function it_extends_Completeness_Meter_operator()
    {
        $this->shouldHaveType('Kibao\CompletenessMeter\Operator');
    }

    function it_has_max_score_be_default()
    {
        $this->getMaxScore()->shouldReturn(10);
    }

    function it_calculates_correct_score_for_given_weight()
    {
        $this->beConstructedWith(array('weight' => 30));
        $this->getMaxScore()->shouldReturn(30);
    }

    function it_calculates_correct_score_for_given_max_elements()
    {
        $this->beConstructedWith(array('maxElements' => 5));
        $this->getMaxScore()->shouldReturn(50);
    }

    function it_calculates_correct_max_score_for_given_weight_and_max_elements()
    {
        $this->beConstructedWith(array('weight' => 100, 'maxElements' => 3));
        $this->getMaxScore()->shouldReturn(300);
    }

    function it_has_weight_by_default()
    {
        $this->getWeight()->shouldReturn(10);
    }

    function its_weight_can_be_set()
    {
        $this->beConstructedWith(array('weight' => 30));
        $this->getWeight()->shouldReturn(30);
    }

    function it_has_max_elements_by_default()
    {
        $this->getMaxElements()->shouldReturn(1);
    }

    function its_max_elements_can_be_set()
    {
        $this->beConstructedWith(array('maxElements' => 30));
        $this->getMaxElements()->shouldReturn(30);
    }

    function its_not_evaluate_correct_flat(\stdClass $std)
    {
        $this->shouldThrow('\InvalidArgumentException')->duringEvaluate(null);
        $this->shouldThrow('\InvalidArgumentException')->duringEvaluate("");

        $this->shouldThrow('\InvalidArgumentException')->duringEvaluate(false);
        $this->shouldThrow('\InvalidArgumentException')->duringEvaluate(0);
        $this->shouldThrow('\InvalidArgumentException')->duringEvaluate("value");
        $this->shouldThrow('\InvalidArgumentException')->duringEvaluate(30);
        $this->shouldThrow('\InvalidArgumentException')->duringEvaluate(30.0);
        $this->shouldThrow('\InvalidArgumentException')->duringEvaluate($std);
    }

    function its_evaluate_correct_empty_array()
    {
        $this->evaluate(array())->getCompletenessScore()->shouldReturn(0);
    }

    function its_evaluate_correct_array_with_empty_values()
    {
        $this->evaluate(array(null))->getCompletenessScore()->shouldReturn(0);
        $this->evaluate(array(null, null))->getCompletenessScore()->shouldReturn(0);
        $this->evaluate(array(null, ""))->getCompletenessScore()->shouldReturn(0);
    }

    function its_evaluate_correct_array()
    {
        $this->evaluate(array(0))->getCompletenessScore()->shouldReturn(10);
        $this->evaluate(array(false))->getCompletenessScore()->shouldReturn(10);
        $this->evaluate(array('value'))->getCompletenessScore()->shouldReturn(10);
        $this->evaluate(array(30))->getCompletenessScore()->shouldReturn(10);
        $this->evaluate(array(30.0))->getCompletenessScore()->shouldReturn(10);
    }

    function its_evaluate_correct_array_with_default_max_elements()
    {
        $this->evaluate(array(0, 'Value', 40.0, false))->getCompletenessScore()->shouldReturn(10);
    }

    function its_evaluate_correct_array_with_max_elements()
    {
        $this->beConstructedWith(array('maxElements' => 3));
        $this->evaluate(array(0))->getCompletenessScore()->shouldReturn(10);
        $this->evaluate(array(0, 10))->getCompletenessScore()->shouldReturn(20);
        $this->evaluate(array(0, 10, 'value'))->getCompletenessScore()->shouldReturn(30);
        $this->evaluate(array(0, 10, 'value', 'value'))->getCompletenessScore()->shouldReturn(30);

        $this->evaluate(array(0, null))->getCompletenessScore()->shouldReturn(10);
        $this->evaluate(array(0, null, 'value'))->getCompletenessScore()->shouldReturn(20);
    }

    function its_evaluate_correct()
    {
        $this->beConstructedWith(array('weight' => 100, 'maxElements' => 3));
        $this->evaluate(array(0))->getCompletenessScore()->shouldReturn(100);
        $this->evaluate(array(false))->getCompletenessScore()->shouldReturn(100);
        $this->evaluate(array('value'))->getCompletenessScore()->shouldReturn(100);
        $this->evaluate(array(30, null))->getCompletenessScore()->shouldReturn(100);
        $this->evaluate(array(30.0, 30))->getCompletenessScore()->shouldReturn(200);
        $this->evaluate(array(30.0, 30, 'a', 'b'))->getCompletenessScore()->shouldReturn(300);
    }
}
