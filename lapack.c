/*
  +----------------------------------------------------------------------+
  | PHP Version 5 / lapac                                                |
  +----------------------------------------------------------------------+
  | Copyright (c) 2012 Ian Barber                                        |
  +----------------------------------------------------------------------+
  | This source file is subject to version 3.0 of the PHP license,       |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.php.net/license/3_0.txt.                                  |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Authors: Ian Barber <ian.barber@gmail.com>                           |
  +----------------------------------------------------------------------+
*/

#include "php_lapack.h"
#include "php_lapack_internal.h"
#include "php_ini.h" /* needed for 5.2 */
#include "Zend/zend_exceptions.h"
#include "ext/standard/info.h"

static zend_class_entry *php_lapack_sc_entry;

static zend_object_handlers lapack_object_handlers;

/* --- Helper Functions --- */

/* {{{ static long* php_lapack_linearize_array(zval *inarray, int *m, int *n)
Transform a PHP array into linear array of longs, and return dimensions 
*/
static double* php_lapack_linearize_array(zval *inarray, int *m, int *n) 
{
	double *outarray; 
	zval **ppzval;
	zval **ppinnerval;
	int i;
	
	/* Set rows num */
	*m = zend_hash_num_elements(Z_ARRVAL_P(inarray));
	outarray = NULL;
	i = 0;

	for (zend_hash_internal_pointer_reset(Z_ARRVAL_P(inarray));
		 zend_hash_get_current_data(Z_ARRVAL_P(inarray), (void **) &ppzval) == SUCCESS;
		 zend_hash_move_forward(Z_ARRVAL_P(inarray))) {
		
		if (Z_TYPE_PP(ppzval) == IS_ARRAY) {
			
			if(outarray == NULL) {
				/* Set columns num and alloc memory for value */
				*n = zend_hash_num_elements(Z_ARRVAL_PP(ppzval));
				outarray = safe_emalloc(*m * *n, sizeof(double), 0);
			}
			
			for (zend_hash_internal_pointer_reset(Z_ARRVAL_PP(ppzval));
				 zend_hash_get_current_data(Z_ARRVAL_PP(ppzval), (void **) &ppinnerval) == SUCCESS;
				 zend_hash_move_forward(Z_ARRVAL_PP(ppzval))) {
					convert_to_double(*ppinnerval);
					outarray[i++] = Z_DVAL_PP(ppinnerval);
			}
			
		}
	}
	
	return outarray;
}
/* }}} */

/* {{{ static zval* php_lapack_reassemble_array(zval *return_value, double *inarray, int m, int n)
Loop through a long array and reassemble into a square php 2d array based on
the height and width supplied
*/
static void php_lapack_reassemble_array(zval *return_value, double *inarray, int m, int n, int stride) 
{
	zval *inner;
	int height, width;
	height = width = 0;
	
	array_init(return_value);
	
	for( height = 0; height < m; height++ ) {
		MAKE_STD_ZVAL(inner);
		array_init(inner);
		for( width = 0; width < n; width++ ) {
			add_next_index_double(inner, inarray[(height*stride)+width]);
		}
		add_next_index_zval(return_value, inner);
	}
	
	return;
}
/* }}} */
	
/* --- Lapack Linear Least Squares Functions --- */

/* {{{ array LapackLeastSquares::simple(array A, array B);
Solve the linear least squares problem, find min x in || B - Ax || 
Returns an array representing x. Expects arrays of arrays, and will 
return an array of arrays in the dimension B num cols x A num cols. 
Uses QR or LQ factorisation on matrix A. 
*/
PHP_METHOD(LapackLeastSquares, byFactorisation)
{
	zval *a, *b;
	double *al, *bl;
	lapack_int info,m,n,lda,ldb,nrhs;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "aa", &a, &b) == FAILURE) {
		return;
	}
	
	al = php_lapack_linearize_array(a, &m, &n);
	bl = php_lapack_linearize_array(b, &m, &nrhs);
	lda = n;
	ldb = nrhs;
	
	info = LAPACKE_dgels(LAPACK_ROW_MAJOR, 'N', m, n, nrhs, al, lda, bl, ldb);
	
	/* TODO: Error handling based on Info  */
	
	php_lapack_reassemble_array(return_value, bl, n, nrhs, ldb);
	
	efree(al);
	efree(bl);
	
	return;
}
/* }}} */

/* {{{ array LapackLeastSquares::withSVD(array A, array B);
Solve the linear least squares problem, find min x in || B - Ax || 
Returns an array representing x. Expects arrays of arrays, and will 
return an array of arrays in the dimension B num cols x A num cols. 
Uses SVD with a divide and conquer algorithm. 
*/
PHP_METHOD(LapackLeastSquares, bySVD)
{
	zval *a, *b;
	double *al, *bl, *s;
	lapack_int info,m,n,lda,ldb,nrhs, rank;
	/* Negative rcond means using default (machine precision) value */
	double rcond = -1.0;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "aa", &a, &b) == FAILURE) {
		return;
	}
	
	al = php_lapack_linearize_array(a, &m, &n);
	bl = php_lapack_linearize_array(b, &m, &nrhs);
	lda = n;
	ldb = nrhs;
	s = safe_emalloc((n < m ? n : m), sizeof(double), 0);
	
	info = LAPACKE_dgelsd(LAPACK_ROW_MAJOR, m, n, nrhs, al, lda, bl, ldb, s, rcond, &rank);
	
	/* TODO: Error handling based on Info  */
	/* Check for convergence */
	if( info > 0 ) {
		// it didn't converge
	}
	
	php_lapack_reassemble_array(return_value, s, 1, (n < m ? n : m), ldb);
	
	efree(al);
	efree(bl);
	efree(s);
	
	return;
}
/* }}} */


/* --- ARGUMENTS AND INIT --- */

ZEND_BEGIN_ARG_INFO_EX(lapack_empty_args, 0, 0, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(lapack_lls_args, 0, 0, 2)
	ZEND_ARG_INFO(0, a)
	ZEND_ARG_INFO(0, b)
ZEND_END_ARG_INFO()

static zend_function_entry php_lapackls_class_methods[] =
{
	PHP_ME(LapackLeastSquares, byFactorisation,	lapack_lls_args, ZEND_ACC_PUBLIC | ZEND_ACC_STATIC)
	PHP_ME(LapackLeastSquares, bySVD,	lapack_lls_args, ZEND_ACC_PUBLIC | ZEND_ACC_STATIC)
	{ NULL, NULL, NULL }
};

PHP_MINIT_FUNCTION(lapack)
{
	zend_class_entry ce;
	memcpy(&lapack_object_handlers, zend_get_std_object_handlers(), sizeof(zend_object_handlers));

	INIT_CLASS_ENTRY(ce, "LapackLeastSquares", php_lapackls_class_methods);
	ce.create_object = NULL;
	lapack_object_handlers.clone_obj = NULL;
	php_lapack_sc_entry = zend_register_internal_class(&ce TSRMLS_CC);

	return SUCCESS;
}

PHP_MSHUTDOWN_FUNCTION(lapack)
{
	UNREGISTER_INI_ENTRIES();
	return SUCCESS;
}

PHP_MINFO_FUNCTION(lapack)
{
	php_info_print_table_start();
		php_info_print_table_header(2, "LAPACK extension", "enabled");
		php_info_print_table_row(2, "LAPACK extension version", PHP_LAPACK_EXTVER);
	php_info_print_table_end();

	DISPLAY_INI_ENTRIES();
}

/* No global functions */
zend_function_entry lapack_functions[] = {
	{NULL, NULL, NULL} 
};

zend_module_entry lapack_module_entry =
{
	STANDARD_MODULE_HEADER,
	PHP_LAPACK_EXTNAME,
	lapack_functions,				/* Functions */
	PHP_MINIT(lapack),				/* MINIT */
	PHP_MSHUTDOWN(lapack),			/* MSHUTDOWN */
	NULL,						/* RINIT */
	NULL,						/* RSHUTDOWN */
	PHP_MINFO(lapack),				/* MINFO */
	PHP_LAPACK_EXTVER,				/* version */
	STANDARD_MODULE_PROPERTIES
};


#ifdef COMPILE_DL_LAPACK
ZEND_GET_MODULE(lapack)
#endif /* COMPILE_DL_LAPACK */
