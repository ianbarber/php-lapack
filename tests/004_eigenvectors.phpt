--TEST--
Calculate the eigenvalues and the left and right eigenvectors of a matrix
--SKIPIF--
<?php
if (!extension_loaded('lapack')) die('skip');
?>
--FILE--
<?php

function printEigVal($e) {
    echo ($e < 0 ? '' : ' '), sprintf("%01.2f", $e);
}

function printEig($e) {
    foreach( $e as $row ) {
        foreach( $row as $col ) {
            if(count($col) == 1) {
                echo " ", printEigVal($col[0]);
            } else {
                echo "( ", printEigVal($col[0]), ", ", printEigVal($col[1]), ") ";
            }
        }
        echo "\n";
    }
    echo "\n";
}

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

$leftEig = array();
$result = Lapack::eigenValues($a, $leftEig);
echo "Left eigenvectors\n";
printEig($leftEig);

$rightEig = array();
$result = Lapack::eigenValues($a, null, $rightEig);
echo "Right eigenvectors\n";
printEig($rightEig);

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
Left eigenvectors
(  0.04,  0.29) (  0.04, -0.29) ( -0.13, -0.33) ( -0.13,  0.33)   0.04
(  0.62,  0.00) (  0.62,  0.00) (  0.69,  0.00) (  0.69,  0.00)   0.56
( -0.04, -0.58) ( -0.04,  0.58) ( -0.39, -0.07) ( -0.39,  0.07)  -0.13
(  0.28,  0.01) (  0.28, -0.01) ( -0.02, -0.19) ( -0.02,  0.19)  -0.80
( -0.04,  0.34) ( -0.04, -0.34) ( -0.40,  0.22) ( -0.40, -0.22)   0.18

Right eigenvectors
(  0.11,  0.17) (  0.11, -0.17) (  0.73,  0.00) (  0.73,  0.00)   0.46
(  0.41, -0.26) (  0.41,  0.26) ( -0.03, -0.02) ( -0.03,  0.02)   0.34
(  0.10, -0.51) (  0.10,  0.51) (  0.19, -0.29) (  0.19,  0.29)   0.31
(  0.40, -0.09) (  0.40,  0.09) ( -0.08, -0.08) ( -0.08,  0.08)  -0.74
(  0.54,  0.00) (  0.54,  0.00) ( -0.29, -0.49) ( -0.29,  0.49)   0.16
