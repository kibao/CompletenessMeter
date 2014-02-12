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

class ExistSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Kibao\CompletenessMeter\Operator\Exist');
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

    function it_has_weight_by_default()
    {
        $this->getWeight()->shouldReturn(10);
    }

    function its_weight_can_be_set()
    {
        $this->beConstructedWith(array('weight' => 30));
        $this->getWeight()->shouldReturn(30);
    }

    function its_evaluate_correct_flat_values(\stdClass $std)
    {
        $this->evaluate(null)->getCompletenessScore()->shouldReturn(0);
        $this->evaluate("")->getCompletenessScore()->shouldReturn(0);
        $this->evaluate(false)->getCompletenessScore()->shouldReturn(10);
        $this->evaluate(0)->getCompletenessScore()->shouldReturn(10);
        $this->evaluate("value")->getCompletenessScore()->shouldReturn(10);
        $this->evaluate(30)->getCompletenessScore()->shouldReturn(10);
        $this->evaluate(30.0)->getCompletenessScore()->shouldReturn(10);
        $this->evaluate($std)->getCompletenessScore()->shouldReturn(10);
    }

    function its_evaluate_correct_array()
    {
        $this->evaluate(array())->getCompletenessScore()->shouldReturn(10);
        $this->evaluate(array(null))->getCompletenessScore()->shouldReturn(10);
        $this->evaluate(array(null, ""))->getCompletenessScore()->shouldReturn(10);
        $this->evaluate(array(0, 10, 'value', 'value'))->getCompletenessScore()->shouldReturn(10);
    }

}
