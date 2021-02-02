<?php declare (strict_types = 1);

use PHPUnit\Framework\TestCase;

require_once "resources/utils.php";

final class UtilsTest extends TestCase
{
    public function testDeniedKeys()
    {
        // Empty arrays
        $this->assertEquals([], array_diff_assoc(
            [],
            denied_keys([], [])
        ));
        // General case
        $this->assertEquals([], array_diff_assoc(
            ["b" => "B", "c" => "C"],
            denied_keys(["a" => "A", "b" => "B", "c" => "C"], ["a", "d"])
        ));
    }

    public function testAllowedKeys()
    {
        // Empty arrays
        $this->assertEquals([], array_diff_assoc(
            [],
            allowed_keys([], [])
        ));
        //
        $this->assertEquals([], array_diff_assoc(
            [],
            allowed_keys(["a" => "A", "b" => "B", "c" => "C"], [])
        ));
        // General case
        $this->assertEquals([], array_diff_assoc(
            ["a" => "A"],
            allowed_keys(["a" => "A", "b" => "B", "c" => "C"], ["a", "d"])
        ));
    }

    public function testApplyMaps()
    {
        $output = apply_maps(["a" => "A", "b" => "B", "c" => "C"], [
            "a" => function ($v) {return "X";},
            "b" => "Y",
            "d" => "Z",
        ]);
        $this->assertEquals([], array_diff_assoc(
            ["a" => "X", "b" => "Y", "c" => "C"],
            $output
        ));
    }

    public function testArrayAny()
    {
        //
        $this->assertTrue(array_any(
            [1, 2, 3],
            function ($v) {return $v > 0;}));
        $this->assertTrue(array_any(
            [-1, 2, -3],
            function ($v) {return $v > 0;}));
        $this->assertFalse(array_any(
            [-1, -2, -3],
            function ($v) {return $v > 0;}));
    }

    public function testArrayEvery()
    {
        //
        $this->assertTrue(array_every(
            [1, 2, 3],
            function ($v) {return $v > 0;}));
        $this->assertFalse(array_every(
            [-1, 2, -3],
            function ($v) {return $v > 0;}));
        $this->assertFalse(array_every(
            [-1, -2, -3],
            function ($v) {return $v > 0;}));
    }

    public function testGroupJoin()
    {
        $this->assertEquals("()", group_join("", []));
        $this->assertEquals("(A) (B)", group_join(" ", ["A", "B"]));
        $this->assertEquals("(A)AND(B)", group_join("AND", ["A", "B"]));
    }
}
