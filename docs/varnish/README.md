eZ Platform Varnish configuration
=================================

Prerequisites
-------------
* A working Varnish 4.1 and higher setup with xkey module installed
* Varnish Plus comes with xkey out of the box and can also be used.

Recommended VCL base files
--------------------------
For Varnish to work properly with eZ, you'll need to use the provided configuration:

* [eZ Platform optimized Varnish VCL](vcl/varnish4.vcl)

For tuning the VCL further to you needs, see the following relevant examples:
- [FOSHttpCache documentation](http://foshttpcache.readthedocs.io/en/1.4/varnish-configuration.html)
- [Symfony documentation](http://symfony.com/doc/2.8/http_cache/varnish.html)


Example installation on Debian/Ubuntu:
--------------------------------------
Starting with Debian 9 and Ubuntu 16.10 installation of `xkey` VMOD is greatly
simplified as new [varnish-modules](https://github.com/varnish/varnish-modules) package now exists.

Install:
```bash
# If you haven't updated package meta info in a while
apt-get update -q -y

# Install varnish and varnish-modules
# optionally: make sure we have ca-certificates to be able to skip optional dependencies
apt-get install -q -y --force-yes --no-install-recommends ca-certificates varnish-modules varnish
```

_You can now start Varnish with flag to use your customized version of "eZ Platform optimized VCL" above._
