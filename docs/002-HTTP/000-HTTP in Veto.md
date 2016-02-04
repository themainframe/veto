# HTTP

HTTP (Hypertext Transfer Protocol) is how your application communicates with the outside world.

## HTTP in Veto

Veto makes the common abstraction of a class for both a HTTP Request and a Response. The entire application can be viewed as a
pipeline to convert a `Request` object into a `Response` object.

HTTP messages in Veto use the [PSR-7](http://www.php-fig.org/psr/psr-7/) HTTP Message Interface.