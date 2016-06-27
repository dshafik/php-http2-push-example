#!/bin/bash
node ./php-http2-push-example/node-server/index.js
nghttpd --htdocs=./curl-http2-dev --verbose --echo-upload --push=/index.html=/LICENSE --push=/index.html=/README.md 8443 ./curl-http2-dev/privkey.pem ./curl-http2-dev/server.pem &
sleep 2
echo "Running test: Accepting Push — nghttpd"
./php-src/sapi/cli/php ./php-http2-push-example/push-ok.php https://localhost:8443/index.html
echo "Running test: Accepting Push — node-http2"
./php-src/sapi/cli/php ./php-http2-push-example/push-ok.php https://localhost:8080/index.html
echo "Running test: Deny Push — nghttpd"
./php-src/sapi/cli/php ./php-http2-push-example/push-deny.php https://localhost:8443/index.html
echo "Running test: Deny Push — node-http2"
./php-src/sapi/cli/php ./php-http2-push-example/push-deny.php https://localhost:8080/index.html
# Useful when running the container interactively
NGHTTPD_PID=$(ps aux | grep nghttpd | grep -v grep | awk -F ' ' '{print $2}')
NODE_PID=$(ps aux | grep node| grep -v grep | awk -F ' ' '{print $2}')
kill -9 $NGHTTPD_PID $NODE_PID
