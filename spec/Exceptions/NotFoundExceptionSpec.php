<?php

namespace spec\Texboy\BadDi\Exceptions;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Texboy\BadDi\Exceptions\NotFoundException;

class NotFoundExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(NotFoundException::class);
    }

    function it_implements_interface()
    {
        $this->shouldImplement('Psr\Container\NotFoundExceptionInterface');
    }

    function it_implements_throwable()
    {
        $this->shouldImplement('Throwable');
    }
}
