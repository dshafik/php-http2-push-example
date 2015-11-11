require('daemon')();
var h2 = require('http2');
var fs = require('fs');
var log = require('bunyan').createLogger({name: 'server'});

var options = {
  key: fs.readFileSync('./cert/localhost.key'),
  cert: fs.readFileSync('./cert/localhost.crt')
};

var server = h2.createServer(options, function(req, res) {
  console.log(req);
  if (req.url != '/main.js' && res.push) {
	var push = res.push('/main.js');
	push.writeHead(200);
	push.end('alert("hello from push stream!")');
  }

  res.writeHead(200);
  res.end('Hello World! <script src="/main.js"></script>');
});

server.listen(8080, "0.0.0.0");
