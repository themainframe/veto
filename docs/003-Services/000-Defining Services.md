# Services

Veto employs the Service Locator/Container pattern to manage interdependencies between classes within your application.

You can declare your classes as services in the configuration files. Veto can automatically pass in dependencies when the classes are instantiated.

## Defining Services

Services are defined in the `services` configuration key. A typical service definition might look like this:

    services:
        someclass:
            class: \Foo\SomeClass
        myclass:
            class: \Bar\MyClass
            parameters:
              - "@someclass"

With this configuration, when the `myclass` service is first instantiated, the `someclass` instance is passed as the first constructor argument.
