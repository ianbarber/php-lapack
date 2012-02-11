--TEST--
Test solving a system of linear equations
--SKIPIF--
<?php
if (!extension_loaded('lapack')) die('skip');
?>
--FILE--
<?php

$a = array(
    array( 6.80,  -6.05,  -0.45,   8.32,  -9.67   ),
    array(-2.11,  -3.30,   2.58,   2.71,  -5.14   ),
    array( 5.66,   5.36,  -2.70,   4.35,  -7.26   ),
    array( 5.97,  -4.44,   0.27,  -7.17,   6.08   ),
    array( 8.23,   1.08,   9.04,   2.14,  -6.87   ),
);

$b = array(
   array(  4.02,  -1.56,   9.81   ),
   array(  6.19,   4.00,  -4.09   ),
   array( -8.22,  -8.67,  -4.57   ),
   array( -7.57,   1.75,  -8.61   ),
   array( -3.03,   2.86,   8.99   ),
);

$result = Lapack::solveLinearEquation($a, $b);
// round the result so we have a chance of matching in the face of float variance
foreach($result as $k => $r) {
    foreach($r as $ik => $ir) {
        $result[$k][$ik] = round($ir, 2);
    }
}
var_dump($result);

?>
--EXPECT--
array(5) {
  [0]=>
  array(3) {
    [0]=>
    float(-0.8)
    [1]=>
    float(-0.39)
    [2]=>
    float(0.96)
  }
  [1]=>
  array(3) {
    [0]=>
    float(-0.7)
    [1]=>
    float(-0.55)
    [2]=>
    float(0.22)
  }
  [2]=>
  array(3) {
    [0]=>
    float(0.59)
    [1]=>
    float(0.84)
    [2]=>
    float(1.9)
  }
  [3]=>
  array(3) {
    [0]=>
    float(1.32)
    [1]=>
    float(-0.1)
    [2]=>
    float(5.36)
  }
  [4]=>
  array(3) {
    [0]=>
    float(0.57)
    [1]=>
    float(0.11)
    [2]=>
    float(4.04)
  }
}