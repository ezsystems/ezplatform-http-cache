// Our Backend - Assuming that web server is listening on port 80
// Replace the host to fit your setup
//
// For additional example see:
// https://github.com/ezsystems/ezplatform/blob/master/doc/docker/entrypoint/varnish/parameters.vcl

backend ezplatform {
    .host = "127.0.0.1";
    .port = "80";
}

// ACL for invalidators IP
//
// Alternative using ACL_INVALIDATE_TOKEN : VCL code also allows for token based invalidation, to use it define a
//      shared secret using env variable ACL_INVALIDATE_TOKEN and eZ Platform will also use that for configuring this
//      bundle. This is prefered for setups such as platform.sh/eZ Platform Cloud, where circular service dependency is
//      unwanted. If you use this, use a strong cryptological secure hash & make sure to keep the token secret.
acl invalidators {
    "127.0.0.1";
    "192.168.0.0"/16;
}

// ACL for debuggers IP
acl debuggers {
    "127.0.0.1";
    "192.168.0.0"/16;
}
