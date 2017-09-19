<?php


namespace Elucidate\Tests;

use PHPUnit\Framework\TestCase;

class WasCalled
{
    private $fn;
    private $times = 0;
    private $name;

    public function __construct($name, callable $fn = null)
    {
        $this->name = $name;
        $this->fn = $fn;
    }

    public function __invoke(...$args)
    {
        $this->times += 1;
        if (!$this->fn) {
            return null;
        }
        $fn = $this->fn;
        return $fn(...$args);
    }

    public function assertWasCalled()
    {
        $name = $this->name;
        TestCase::assertThat($this->times > 0, TestCase::isTrue(), "Failed asserting '$name' was called");
    }

    public function assertWasCalledAtLeast($int = 1)
    {
        $name = $this->name;
        TestCase::assertThat($this->times >= $int, TestCase::isTrue(), "Failed asserting '$name' was called at least $int time(s), was called " . $this->times . " time(s)");
    }

    public function assertWasCalledExactly(int $times)
    {
        $name = $this->name;
        TestCase::assertThat($this->times === $times, TestCase::isTrue(), "Failed asserting '$name' was called exactly $times time(s), was called " . $this->times . " time(s)");
    }

    public function assertWasNotCalled()
    {
        $name = $this->name;
        TestCase::assertThat($this->times === 0, TestCase::isTrue(), "Failed asserting '$name' was called not called, was called " . $this->times . " time(s)");
    }
}
