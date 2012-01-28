/*
  +----------------------------------------------------------------------+
  | PHP Version 5 / lapack                                               |
  +----------------------------------------------------------------------+
  | Copyright (c) 2011 Ian Barber                                        |
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
#ifndef _PHP_LAPACK_H_
# define _PHP_LAPACK_H_

#define PHP_LAPACK_EXTNAME "lapack"
#define PHP_LAPACK_EXTVER  "0.1.0"

#ifdef HAVE_CONFIG_H
# include "config.h"
#endif

#ifdef ZTS
# include "TSRM.h"
#endif

#include "php.h"

#ifdef ZTS
# define LAPACK_G(v) TSRMG(lapack_globals_id, zend_lapack_globals *, v)
#else
# define LAPACK_G(v) (lapack_globals.v)
#endif

extern zend_module_entry lapack_module_entry;
#define phpext_lapack_ptr &lapack_module_entry

#endif /* _PHP_LAPACK_H_ */


