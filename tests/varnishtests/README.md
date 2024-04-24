# Varnishtest files

Varnishtests can be ran using the official docker images:

Varnish6:
    docker run --rm --entrypoint varnishtest --volume $PWD:/etc/varnish varnish:stable -t 5 -D vclfile=varnish6.vcl tests/varnishtests/001-simple-request.vtc

Varnish7:
    docker run --rm --entrypoint varnishtest --volume $PWD:/etc/varnish varnish:fresh -t 5  -D vclfile=varnish7.vcl tests/varnishtests/001-simple-request.vtc
