LightService
===
LightService is a lightweight rpc framework with pluginable transport and message implementation.

Requirements
===

jsclient
--------
- [jquery](http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js)
- [json2](http://cdn.staticfile.org/json2/20130526/json2.min.js)

Tips
===

* try [example](./exmple/). There are two examples with http and zmq.
    - For http transport, start one http server, e.g. `php -S 0.0.0.0:8080`, then `php client.php`
    - For zmq transport, start zmq service(refer to [serverbench.php](http://git.ipo.com/yuanbaoju/serverbench-php) for more info), then `php client.php`

Release Note
===

* version 2.0.0 has supported [jsonrpc 2.0.0](http://www.jsonrpc.org/specification) and ZMQ transport
* upholding the [Semantic Versioning Specification](http://semver.org/).
