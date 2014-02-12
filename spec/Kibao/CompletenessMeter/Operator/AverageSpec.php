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

use Kibao\CompletenessMeter\CompletenessSummary;
use Kibao\CompletenessMeter\Operator;
use PhpSpec\ObjectBehavior;

class AverageSpec extends ObjectBehavior
{
    function let(Operator $operator)
    {
        $this->beConstructedWith(array('operator' => $operator));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Kibao\CompletenessMeter\Operator\Average');
    }

    function it_extends_Completeness_Meter_operator()
    {
        $this->shouldHaveType('Kibao\CompletenessMeter\Operator');
    }

    function it_has_weight_by_default()
    {
        $this->getWeight()->shouldReturn(10);
    }

    function its_weight_can_be_set(Operator $operator)
    {
        $this->beConstructedWith(array('operator' => $operator, 'weight' => 30));
        $this->getWeight()->shouldReturn(30);
    }

    function its_evaluate_correct_completeness_score(Operator $operator, CompletenessSummary $summary0, CompletenessSummary $summary1, CompletenessSummary $summary2)
    {
        $operator->getMaxScore()->willReturn(100);
        $this->beConstructedWith(array('operator' => $operator, 'weight' => 200));
        $summary0->isPassed()->willReturn(false);
        $summary0->getCompletenessScore()->willReturn(0);
        $summary1->isPassed()->willReturn(true);
        $summary1->getCompletenessScore()->willReturn(100);
        $summary2->isPassed()->willReturn(true);
        $summary2->getCompletenessScore()->willReturn(50);

        $operator->evaluate(0)->willReturn($summary0);
        $operator->evaluate(1)->willReturn($summary1);
        $operator->evaluate(2)->willReturn($summary2);

        $this->evaluate(array(0, 1))->getCompletenessScore()->shouldReturn(100);
        $this->evaluate(array(1, 1))->getCompletenessScore()->shouldReturn(200);
        $this->evaluate(array(0, 1, 2))->getCompletenessScore()->shouldReturn(100);
        $this->evaluate(array(0, 0, 2))->getCompletenessScore()->shouldReturn(33);
    }

    function it_no_evaluate_flat_values(\stdClass $std)
    {
        $this->shouldThrow('\InvalidArgumentException')->duringEvaluate(null);
        $this->shouldThrow('\InvalidArgumentException')->duringEvaluate(0);
        $this->shouldThrow('\InvalidArgumentException')->duringEvaluate('value');
        $this->shouldThrow('\InvalidArgumentException')->duringEvaluate($std);
    }
}
