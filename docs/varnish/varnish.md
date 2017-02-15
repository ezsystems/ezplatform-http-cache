eZ Platform Varnish configuration
=================================

Prerequisites
-------------
* A working Varnish 4.1 and higher setup with xkey module installed.

Recommended VCL base files
--------------------------
For Varnish to work properly with eZ, you'll need to use the provided configuration:

* [eZ Platform optimized Varnish VCL](vcl/varnish4.vcl)

> **Note:** Http cache management is done with the help of [FOSHttpCacheBundle](http://foshttpcachebundle.readthedocs.org/).
  One may need to tweak their VCL further on according to [FOSHttpCache documentation](http://foshttpcache.readthedocs.org/en/latest/varnish-configuration.html)
  in order to use features supported by it.
