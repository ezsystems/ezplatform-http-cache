eZ Platform Varnish configuration
=================================

Prerequisites
-------------
* A working Varnish 4.1 and higher setup with xkey module installed
  * _As of eZ Platform 2.5LTS the requirement is Varnish 5.1 (6.0LTS recommended and what we mainly test against)_
  * ezplatform-http-cache 0.9+ requires varnish-modules 0.10.2 or higher, use 0.8 if you need to use varnish-modules 0.9
* Varnish Plus comes with xkey out of the box and can also be used.

Recommended VCL base files
--------------------------
For Varnish to work properly with eZ, you'll need to use the provided configuration:

* [eZ Platform 1.13LTS optimized Varnish 4+ VCL](vcl/varnish4.vcl)
* [eZ Platform 2.5LTs optimized Varnish 5.1+ VCL](vcl/varnish5.vcl)

For tuning the VCL further to you needs, see the following relevant examples:
- [FOSHttpCache documentation](http://foshttpcache.readthedocs.io/en/1.4/varnish-configuration.html)
- Symfony documentation [2.8](http://symfony.com/doc/2.8/http_cache/varnish.html) [3.4](http://symfony.com/doc/3.4/http_cache/varnish.html)
- [xkey vmod doc](https://github.com/varnish/varnish-modules/blob/master/docs/vmod_xkey.rst)
- [General VCL Varnish doc](https://www.varnish-cache.org/docs/trunk/users-guide/vcl.html)


Example installation on Debian/Ubuntu:
--------------------------------------
Starting with Debian 9 and Ubuntu 18.04 installation of `xkey` VMOD is greatly
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
