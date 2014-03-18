<?php

namespace Ouzo\Tests\Mock;


use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Objects;

class MethodCallMatcher
{
    private $name;
    private $arguments;

    function __construct($name, $arguments)
    {
        $this->name = $name;
        $this->arguments = $arguments;
    }

    public function matches(MethodCall $methodCall)
    {
        if ($methodCall->name != $this->name) {
            return false;
        }

        if (Arrays::firstOrNull($this->arguments) instanceof AnyArgumentList) {
            return true;
        }

        if (count($methodCall->arguments) != count($this->arguments)) {
            return false;
        }

        foreach ($this->arguments as $i => $arg) {
            if (!$this->argMatches($arg, $methodCall->arguments[$i])) {
                return false;
            }
        }
        return true;
    }

    public function argMatches($expected, $actual)
    {
        return $expected instanceof AnyArgument || $expected == $actual;
    }

    function __invoke(MethodCall $methodCall)
    {
        return $this->matches($methodCall);
    }

}