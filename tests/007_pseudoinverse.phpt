--TEST--
Test calculating the inverse of a matrix
--SKIPIF--
<?php
if (!extension_loaded('lapack')) die('skip');
?>
--FILE--
<?php

$a = array(
    array( 8, 1, 6 ),
    array( 3, 5, 7 ),
    array( 4, 9, 2 ),
);

$result = Lapack::pseudoInverse($a);
// round the result so we have a chance of matching in the face of float variance
foreach($result as $k => $r) {
    foreach($r as $ik => $ir) {
        $result[$k][$ik] = round($ir, 2);
    }
}
var_dump($result);

?>
--EXPECT--
array(3) {
  [0]=>
  array(3) {
    [0]=>
    float(0.15)
    [1]=>
    float(-0.14)
    [2]=>
    float(0.06)
  }
  [1]=>
  array(3) {
    [0]=>
    float(-0.06)
    [1]=>
    float(0.02)
    [2]=>
    float(0.11)
  }
  [2]=>
  array(3) {
    [0]=>
    float(-0.02)
    [1]=>
    float(0.19)
    [2]=>
    float(-0.1)
  }
}