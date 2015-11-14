# PHP HTTP/2 Server Push Example

## What/Why

This is a sample script/docker container build to test a patch that adds support for curls new HTTP/2 server push support.

## Setup

### Docker

Build and run the docker container:

```sh
$ docker build -t $USER/php-http2-push-example .
$ docker run $USER/php-http2-push-example
```

To debug it yourself:

Start the docker container:

```
$ docker run -ti $USER/php-http2-push-example /bin/bash
```

Run nghttpd inside the container from a different terminal:

```
$ docker run -ti $USER/php-http2-push-example nghttpd --htdocs=./curl-http2-dev --verbose --echo-upload --push=/index.html=/LICENSE --push=/index.html=/README.md 8443 ./curl-http2-dev/privkey.pem ./curl-http2-dev/server.pem
```

Run gdb:

```
$ gdb --args php-src/sapi/cli/php ./php-http2-push-example/push.php https://localhost:8443/index.html 
gdb> run
```

## Expected Behavior:
It should spit out three documents, and show a bunch of nghttpd output either intermingle, or if you're
debugging it yourself, then the nghttpd output will be in the second terminal. 

It should also show a `var_dump()` of the arguments passed to the closure.

## Actual Behavior

This currently works as expected except for some memory leaks

## Other Tests

Try it without setting the version to HTTP/2, and against another HTTP/2 enabled site, e.g. https://http2.akamai.net
