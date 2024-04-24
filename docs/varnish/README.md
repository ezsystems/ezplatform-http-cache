eZ Platform Varnish configuration
=================================

Prerequisites
-------------
* A working Varnish 6.0 or higher _(6.0LTS recommended)_
  * With 'xkey' VMOD, correct version is provided with `varnish-modules` 0.10.2 or higher
* Varnish Enterprise comes with xkey out of the box and can also be used.

Recommended VCL base files
--------------------------
For Varnish to work properly with eZ, you'll need to use the provided configuration:

* [eZ Platform 2.5LTs optimized Varnish 6.0+ VCL](vcl/varnish6.vcl)

For tuning the VCL further to you needs, see the following relevant examples:
- [FOSHttpCache documentation](https://foshttpcache.readthedocs.io/en/latest/varnish-configuration.html)
- Symfony documentation [4.4](https://symfony.com/doc/4.4/http_cache/varnish.html)
- [xkey vmod doc](https://github.com/varnish/varnish-modules/blob/master/docs/vmod_xkey.rst)
- [General VCL Varnish doc](https://www.varnish-cache.org/docs/trunk/users-guide/vcl.html)


Example installation on Debian/Ubuntu:
--------------------------------------
As of Debian 10, RHEL 8 _(or RHSCL 3.3)_, and Ubuntu 18.04LTS, installation of `xkey` VMOD is greatly
simplified as supported version of [varnish-modules](https://github.com/varnish/varnish-modules) package is bundled.

Example on installation on Debian/Ubuntu:
```bash
# If you haven't updated package meta info in a while
apt-get update -q -y

# Install varnish and varnish-modules
# optionally: make sure we have ca-certificates to be able to skip optional dependencies
apt-get install -q -y --force-yes --no-install-recommends ca-certificates varnish-modules varnish
```

_You can now start Varnish with flag to use your customized version of "eZ Platform optimized VCL" above._
