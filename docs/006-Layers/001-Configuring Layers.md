
## Configuring Layers

Layers are configured under the `layers` configuration key.

A typical configuration for an application that accepts requests, routes them then dispatches them to controllers might be as follows:

    layers:
        router:
            service: layer.router
            priority: 0
        dispatcher:
            service: layer.dispatcher
            priority: 1

With this configuration, `Request` instances passed to `$app->handle()` will be passed first to the `layer.router` layer service's `in()` method.

This would typically be an instance of `Veto\Layer\Router\RouterLayer`, though may be a custom router implementation implementing the `InboundLayerInterface` interface.

After the `layer.router` layer service has finished processing the `Request`, it will be provided to the next priority level layer service - `layer.dispatcher`. Again, this may be a custom implementation of a dispatcher, but typically might be an instance of `Veto\Layer\Dispatcher\DispatcherLayer`.

### Responses from Layers

The last layer must return a `Response` object.

Layers before the last layer may _short-circuit_ further layers by returning a `Response` object early.

After a `Response` object is obtained, it is passed to the `out()` method of each registered layer service.