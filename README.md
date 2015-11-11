# PHP HTTP/2 Server Push Example

## What/Why

This is a sample script to test a patch that adds support for curls new HTTP/2 server push support.

It includes these instructions, a simple node.js HTTP/2 server with server push support and an example request (`node-server/index.js`) and the PHP script to test the new features.

## Setup

To run this example, you must first install `curl` 7.45.0 with HTTP/2 support.

You can check this with `curl --version`, and make sure `http2` is listed (and that it's >= 7.4.50)

If you use a mac, this can be done with homebrew:

```sh
$ brew install curl --with-nghttp2 --with-openssl
```

You will also need node/npm >= 5.0:

```sh
$ brew install node
```

Once you have this, you will need to compile PHP using the `curl-http2-push` branch in the `dshafik/php-src` fork. First clone the `php/php-src` repo:

```sh
$ git clone https://github.com/php/php-src.git
```

Then add the fork, and check out the branch

```sh
$ git remote add dshafik https://github.com/dshafik/php-src.git
$ git fetch --all
$ git checkout dshafik/curl-http2-push
```

Finally, compile PHP:

```sh
$ ./buildconf
$ CFLAGS='-O0 -ggdb3' ./configure --disable-all --with-curl=/usr/local/Cellar/curl/7.45.0 --enable-debug
$ make -j4
```

Once you've done this, you can execute `./sapi/cli/php` or `./sapi/phpdbg/phpdbg` with the patch,
and compiled against the latest libcurl.

Now, run the http2 server:

```sh
$ cd node-server
$ npm install
$ node index.js
```

In another terminal, test it with `nghttp`:

```sh
$ nghttp -n --max-concurrent-streams=2 --stat http://127.0.0.1:3000/nghttp
``` 

Finally, run the PHP script at the root of the checkout:

```sh
$ php ./push.php
```

## Expected Behavior:
It should spit out _at least_ some HTML, but preferably, some HTML and CSS (with the CSS being pushed)

It should also show a `var_dump()` of the arguments passed to the closure.

## Actual Behavior

It doesn't dump any HTML, but it does `var_dump()`. It then displays an error:

```
php(55493,0x7fff78e50300) malloc: *** error for object 0x10204ac00: pointer being freed was not allocated
*** set a breakpoint in malloc_error_break to debug

Program received signal SIGABRT, Aborted.
0x00007fff8d188286 in __pthread_kill () from /usr/lib/system/libsystem_kernel.dylib
```

## Other Tests

Try it without setting the version to HTTP/2, and against another HTTP/2 enabled site, e.g. https://http2.akamai.net
