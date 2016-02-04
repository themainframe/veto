# Controllers

A controller is a class that contains a number of *action methods*.

Each action method performs a specific part of your application's functionality.

## Writing Controllers

A Veto controller class has no restrictions on inheritance or name. Any class can be used as a controller, however Veto
provides an abstract class - `Veto\MVC\AbstractController` - with useful functionality that you may wish to extend when writing controller classes.

A controller class might look like this:

    class MyController extends AbstractController
    {
        public function homepage(Request $request)
        {
            return new Response('Hello, world!');
        }
    }

Note that when a `Request`-type parameter is hinted in a controller action method, Veto will automagically pass the `Request` object to your controller action method when it is called.

### Architecture

You should try to keep controllers minimal and *skinny*. Any serious business logic should be performed in a separate class that can be injected into your
controllers as a service parameter.