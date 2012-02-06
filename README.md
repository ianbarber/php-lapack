PHP Lapack Extension
=================================

This PHP extension wraps the [LAPACK](http://www.netlib.org/lapack/) Linear Algebra Fortran library, using the LAPACKE C wrapper to allow an easy(ish) way in PHP to get access to some decent linear algebra processing. The extension is currently early days, and wraps just a few of the "driver" functions - functions which solve specific types of problems. Specifically the current extension exposes: 

* Two methods of solving linear least squares problems
* A method to return the singular values of a matrix
* A method to return the eigenvalues of a matrix

All matrices are passed in as nested PHP arrays, and returned the same way. This is certainly not the worlds most memory efficient format, so don't expect to do very large sized matrix transforms. That said, other than the memory usage, the processing should be extremely quick in the library as the wrapping is fairly minimal. 

Usage
=================================

The API is currently presented as static calls on the Lapack class. For example for linear least squares: 

    <?php
    $a = array(
        array( 1.44,  -7.84,  -4.39,   4.53),
        array(-9.96,  -0.28,  -3.24,   3.83),
        array(-7.55,   3.24,   6.27,  -6.64),
        array( 8.34,   8.09,   5.28,   2.06),
        array( 7.08,   2.52,   0.74,  -2.47),
        array(-5.45,  -5.70,  -1.19,   4.70),
    );
    
    $b = array(
        array( 8.58,   9.35),
        array( 8.26,  -4.43),
        array( 8.48,  -0.70),
        array(-5.28,  -0.26),
        array( 5.72,  -7.36),
        array( 8.93,  -2.52),           
    );
    
    $result = Lapack::leastSquaresByFactorisation($a, $b);
    var_dump($result);
    ?>
    
This should print (with some rounding to allow for float differences):

    array(4) {
      [0]=>
      array(2) {
        [0]=>
        float(-0.45)
        [1]=>
        float(0.25)
      }
      [1]=>
      array(2) {
        [0]=>
        float(-0.85)
        [1]=>
        float(-0.9)
      }
      [2]=>
      array(2) {
        [0]=>
        float(0.71)
        [1]=>
        float(0.63)
      }
      [3]=>
      array(2) {
        [0]=>
        float(0.13)
        [1]=>
        float(0.14)
      }
    }

Using the same setup we can do: 

* $result = Lapack::leastSquaresBySVD($a, $b);
* $result = Lapack::singularValues($a);
* $result = Lapack::eigenValues($a);

Installation
=================================

From a limited survey, most distribution LAPACK packages do not include lapacke, so the easiest method is to build from source: 

    svn co https://icl.cs.utk.edu/svn/lapack-dev/lapack/trunk lapack
    cd lapack
    mkdir build
    cd build
    cmake -D BUILD_SHARED_LIBS=ON -D LAPACKE=ON ../
    make 
    sudo make install
    
That should build LAPACK and the C wrapper (lapacke). Installing the extension is as simple as downloading the package, then: 

    phpize
    configure
    make 
    sudo make install

Windows support is currently not included - once the API is stabilised though this will be added relatively shortly.


