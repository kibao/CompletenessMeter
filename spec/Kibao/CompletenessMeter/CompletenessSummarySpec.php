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

class CompletenessSummarySpec extends ObjectBehavior
{
    /**
     * @param \Kibao\CompletenessMeter\Operator $operator
     */
    function let($operator)
    {
        $operator->getMaxScore()->willReturn(100);
        $this->beConstructedWith(
            $operator,
            array(
                'key' => 'value',
            )
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Kibao\CompletenessMeter\CompletenessSummary');
    }

    function it_adds_summary_properly(CompletenessSummary $summary)
    {
        $this->has('key')->shouldReturn(false);

        $this->add('key', $summary);

        $this->has('key')->shouldReturn(true);
    }

    function it_returns_summary_properly(CompletenessSummary $summary)
    {
        $this->add('key', $summary);

        $this->get('key')->shouldReturn($summary);
    }

    function it_returns_passed_properly(CompletenessSummary $summary1, CompletenessSummary $summary2)
    {
        $summary1->isPassed()->willReturn(true);
        $summary2->isPassed()->willReturn(false);
        $this->add('1', $summary1);
        $this->add('2', $summary2);

        $this->shouldNotBePassed();

        $summary1->isPassed()->willReturn(true);
        $summary2->isPassed()->willReturn(true);

        $this->shouldNotBePassed();
    }

    function it_returns_passed_checks_properly(CompletenessSummary $summary1, CompletenessSummary $summary2)
    {
        $summary1->isPassed()->willReturn(true);
        $summary2->isPassed()->willReturn(false);
        $this->add('pass', $summary1);
        $this->add('fail', $summary2);

        $this->getPassedChecks()->shouldHaveValue('pass');
    }

    function it_returns_failed_checks_properly(CompletenessSummary $summary1, CompletenessSummary $summary2)
    {
        $summary1->isPassed()->willReturn(true);
        $summary2->isPassed()->willReturn(false);
        $this->add('pass', $summary1);
        $this->add('fail', $summary2);

        $this->getFailedChecks()->shouldHaveValue('fail');
    }

    function it_returns_proper_subject(Operator $operator, $o)
    {
        $this->beConstructedWith($operator, $o);
        $this->getSubject()->shouldReturn($o);
    }

    function it_calculates_proper_completeness_score(CompletenessSummary $summary1, CompletenessSummary $summary2)
    {
        $summary1->isPassed()->willReturn(true);
        $summary1->getCompletenessScore()->willReturn(50);
        $summary2->isPassed()->willReturn(false);
        $summary2->getCompletenessScore()->willReturn(5);
        $this->add('1', $summary1);
        $this->add('2', $summary2);

        $this->getCompletenessScore()->shouldReturn(55);
    }

    function it_returns_proper_max_score(Operator $operator)
    {
        $operator->getMaxScore()->willReturn(100);
        $this->beConstructedWith($operator, array('key' => 'value',));

        $this->getMaxScore()->shouldReturn(100);
    }

    function it_returns_proper_percent_complete(Operator $operator, CompletenessSummary $summary1, CompletenessSummary $summary2)
    {
        $operator->getMaxScore()->willReturn(100);
        $summary1->isPassed()->willReturn(true);
        $summary1->getCompletenessScore()->willReturn(50);
        $summary2->isPassed()->willReturn(false);
        $summary2->getCompletenessScore()->willReturn(5);

        $this->beConstructedWith($operator, array());
        $this->add('1', $summary1);
        $this->add('2', $summary2);

        $this->getPercentComplete()->shouldReturn(55);
    }

    function getMatchers()
    {
        return array(
            'haveValue' => function ($subject, $value) {
                    return in_array($value, $subject);
                },
        );
    }

}
