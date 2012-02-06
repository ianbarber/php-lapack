--TEST--
Calculate the eigenvalues and the left and right eigenvectors of a matrix
--SKIPIF--
<?php
if (!extension_loaded('lapack')) die('skip');
?>
--FILE--
<?php

$a = array(
    array(-1.01,   0.86,  -4.60,  3.31,  -4.81  ),
    array( 3.98,   0.53,  -7.04,  5.29,   3.55  ),
    array( 3.30,   8.26,  -3.89,  8.20,  -1.51  ),
    array( 4.43,   4.96,  -7.66, -7.33,   6.18  ),
    array( 7.31,  -6.43,  -6.16,  2.47,   5.58  ),
);


$result = Lapack::eigenValues($a);
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
  array(2) {
    [0]=>
    float(2.86)
    [1]=>
    float(10.76)
  }
  [1]=>
  array(2) {
    [0]=>
    float(2.86)
    [1]=>
    float(-10.76)
  }
  [2]=>
  array(2) {
    [0]=>
    float(-0.69)
    [1]=>
    float(4.7)
  }
  [3]=>
  array(2) {
    [0]=>
    float(-0.69)
    [1]=>
    float(-4.7)
  }
  [4]=>
  array(1) {
    [0]=>
    float(-10.46)
  }
}