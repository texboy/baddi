<?php

namespace spec\Texboy\BadDi\Exceptions;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Texboy\BadDi\Exceptions\ContainerException;

class ContainerExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ContainerException::class);
    }

    function it_implements_interface()
    {
        $this->shouldImplement('Psr\Container\ContainerExceptionInterface');
    }

    function it_implements_throwable()
    {
        $this->shouldImplement('Throwable');
    }
}
