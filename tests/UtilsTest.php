<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once "resources/utils.php";

final class UtilsTest extends TestCase {
  public function testApplyMaps() {
    function map($v) {return "X";}

    $output = apply_maps([
      "a" => "A",
      "b" => "B",
      "c" => "C"
    ], [
      "a" => "map",
      "b" => "Y",
      "d" => "Z"
    ]);
    $this->assertArrayHasKey("a", $output);
    $this->assertArrayHasKey("b", $output);
    $this->assertArrayHasKey("c", $output);
    $this->assertArrayNotHasKey("d", $output);
    $this->assertEquals("X", $output["a"]);
    $this->assertEquals("Y", $output["b"]);
    $this->assertEquals("C", $output["c"]);
  }

  public function testArrayAny() {
    //
    $this->assertTrue(array_any([1,2,3], function($v){return $v > 0;}));
    $this->assertTrue(array_any([-1,2,-3], function($v){return $v > 0;}));
    $this->assertFalse(array_any([-1,-2,-3], function($v){return $v > 0;}));
  }

  public function testArrayEvery() {
    //
    $this->assertTrue(array_every([1,2,3], function($v){return $v > 0;}));
    $this->assertFalse(array_every([-1,2,-3], function($v){return $v > 0;}));
    $this->assertFalse(array_every([-1,-2,-3], function($v){return $v > 0;}));
  }
}

?>
