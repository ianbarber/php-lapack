--TEST--
Calculate the singular values of a matrix
--SKIPIF--
<?php
if (!extension_loaded('lapack')) die('skip');
?>
--FILE--
<?php

$a = array(
    array( 7.52,  -1.10,  -7.95,  1.08  ),
    array(-0.76,   0.62,   9.34, -7.10  ),
    array( 5.13,   6.62,  -5.66,  0.87  ),
    array(-4.75,   8.52,   5.75,  5.30  ),
    array( 1.33,   4.91,  -5.49, -3.52  ),
    array(-2.40,  -6.77,   2.34,  3.95  ),
);


$result = Lapack::singularValues($a);
// round the result so we have a chance of matching in the face of float variance
foreach($result as $k => $r) {
    foreach($r as $ik => $ir) {
        $result[$k][$ik] = round($ir, 2);
    }
}
var_dump($result);

?>
--EXPECT--
array(1) {
  [0]=>
  array(4) {
    [0]=>
    float(18.37)
    [1]=>
    float(13.63)
    [2]=>
    float(10.85)
    [3]=>
    float(4.49)
  }
}