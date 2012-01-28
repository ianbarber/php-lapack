--TEST--
Test s 
--SKIPIF--
<?php
if (!extension_loaded('lapack')) die('skip');
?>
--FILE--
<?php

$a = array(
    array(1, 1, 1),
    array(2, 3, 4),
    array(3, 5, 2),
    array(4, 2, 5),
    array(5, 4, 3),    
);

$b = array(
    array(-10, -3),
    array(12, 14),
    array(14, 12),
    array(16, 16),        
    array(18, 16),            
);

$result = LapackLeastSquare::simple($a, $b);
$result2 = LapackLeastSquare::withSVD($a, $b);
var_dump($result);
var_dump($result == $result2);

?>
--EXPECT--
array(3) {
  [0]=>
  array(2) {
    [0]=>
    int(2)
    [1]=>
    int(1)
  }
  [1]=>
  array(2) {
    [0]=>
    int(1)
    [1]=>
    int(1)
  }
  [2]=>
  array(2) {
    [0]=>
    int(1)
    [1]=>
    int(1)
  }
}
bool(true)