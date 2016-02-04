
## Locating Services

You can locate a named service at any time using Veto's **Service Locator**:

    $app->container->get('myclass');  // Get the instance of \\Bar\MyClass

Be aware that use of the service locator can negate the benefits of using services at all. You should aim, whenever possible, to have dependencies passed into the constructors of your classes using the `parameters` option described above.
