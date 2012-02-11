--TEST--
Test retrieving identity matrices
--SKIPIF--
<?php
if (!extension_loaded('lapack')) die('skip');
?>
--FILE--
<?php

var_dump(Lapack::identity(2));
var_dump(Lapack::identity(5));

?>
--EXPECT--
array(2) {
  [0]=>
  array(2) {
    [0]=>
    float(1)
    [1]=>
    float(0)
  }
  [1]=>
  array(2) {
    [0]=>
    float(0)
    [1]=>
    float(1)
  }
}
array(5) {
  [0]=>
  array(5) {
    [0]=>
    float(1)
    [1]=>
    float(0)
    [2]=>
    float(0)
    [3]=>
    float(0)
    [4]=>
    float(0)
  }
  [1]=>
  array(5) {
    [0]=>
    float(0)
    [1]=>
    float(1)
    [2]=>
    float(0)
    [3]=>
    float(0)
    [4]=>
    float(0)
  }
  [2]=>
  array(5) {
    [0]=>
    float(0)
    [1]=>
    float(0)
    [2]=>
    float(1)
    [3]=>
    float(0)
    [4]=>
    float(0)
  }
  [3]=>
  array(5) {
    [0]=>
    float(0)
    [1]=>
    float(0)
    [2]=>
    float(0)
    [3]=>
    float(1)
    [4]=>
    float(0)
  }
  [4]=>
  array(5) {
    [0]=>
    float(0)
    [1]=>
    float(0)
    [2]=>
    float(0)
    [3]=>
    float(0)
    [4]=>
    float(1)
  }
}