PHP_ARG_WITH(lapack, whether to enable lapack support,
[  --with-lapack[=DIR]       Enable lapack support. DIR is the prefix to the library installation directory.], yes)

if test "$PHP_LAPACK" != "no"; then


dnl Get PHP version depending on shared/static build

  AC_MSG_CHECKING([PHP version is at least 5.2.0])

  if test -z "${PHP_VERSION_ID}"; then
    if test -z "${PHP_CONFIG}"; then
      AC_MSG_ERROR([php-config not found])
    fi
    PHP_LAPACK_FOUND_VERNUM=`${PHP_CONFIG} --vernum`;
    PHP_LAPACK_FOUND_VERSION=`${PHP_CONFIG} --version`
  else
    PHP_LAPACK_FOUND_VERNUM="${PHP_VERSION_ID}"
    PHP_LAPACK_FOUND_VERSION="${PHP_VERSION}"
  fi

  if test "$PHP_LAPACK_FOUND_VERNUM" -ge "50200"; then
    AC_MSG_RESULT(yes. found $PHP_LAPACK_FOUND_VERSION)
  else 
    AC_MSG_ERROR(no. found $PHP_LAPACK_FOUND_VERSION)
  fi

  AC_MSG_CHECKING([for lapacke.h header])
  for i in $PHP_LAPACK /usr/local /usr;
  do
    test -r $i/include/lapacke.h && LAPACK_PREFIX=$i && LAPACK_INC_DIR=$i/include && LAPACK_OK=1
  done
  	
  if test "$LAPACK_OK" != "1"; then
    AC_MSG_ERROR([Unable to find lapacke.h])
  fi
  
  AC_MSG_RESULT([found in $LAPACK_INC_DIR])
  
  AC_MSG_CHECKING([for lapacke shared libraries])
  PHP_CHECK_LIBRARY(lapacke, LAPACKE_dgesv, [
    PHP_ADD_LIBRARY_WITH_PATH(lapacke, $LAPACK_PREFIX/lib, LAPACK_SHARED_LIBADD)
    PHP_ADD_INCLUDE($LAPACK_INC_DIR)
  ],[
    AC_MSG_ERROR([not found. Make sure that lapacke is installed])
  ],[
    LAPACK_SHARED_LIBADD -llapacke
  ])
  
  
  PHP_NEW_EXTENSION(lapack, lapack.c, $ext_shared)
  AC_DEFINE(HAVE_LAPACK,1,[ ])

  PHP_SUBST(LAPACK_SHARED_LIBADD)
fi

