# HTTP

[HTTP](http://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol) (Hypertext Transfer Protocol) is how your application communicates with the outside world.

## HTTP in Veto

Veto makes the common abstraction of a class for both a HTTP `Request` and a `Response`. The entire application can be viewed as a pipeline to convert a `Request` object into a `Response` object.

Veto adheres to the [PSR-7 standard](https://github.com/php-fig/fig-standards/blob/master/proposed/http-message.md) for HTTP Messages.

## Requests

A Veto `Request` object wraps a HTTP request into a single object instance.

### Request Parameters

A request has a number of parameter sets associated with it. For instance, the Query String variables can be accessed using the `getQueryParams()` method of the `Request` object. It contains an instance of the `\Veto\Collection\Bag` class which is a key-value pair store abstraction providing a number of useful methods.

### Method

The HTTP method name of a `Request` object may be accessed using the `getMethod()` method.