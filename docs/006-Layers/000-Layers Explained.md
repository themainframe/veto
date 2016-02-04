# Layers

Layers are pieces of middleware that sit between the Veto kernel and your controllers.

The Router is an example of a layer. The router tags incoming requests so that the kernel knows which controller action method they should be handled by.

You can define your own layers to perform other functions - for example security, caching or templating.

## Layers Explained

When a new Request arrives at the Veto kernel, it is passed through each of the registered layers in the application in the order that they are
specified in the application configuration. The `in()` method is called on each layer and the `Request` object is passed to it.

Once all layers have been executed, the request is handled in the usual way.

After a `Response` object is received from the handling controller, it is passed to the `out()` method of each registered layer. Once each layer has been
executed again, the application execution is complete.

A layer class is simply a class that implements the `LayerInterface` interface.

The `LayerInterface` interface mandates that a class must implement the `in(Request $request)` and `out(Response $response)` methods.
