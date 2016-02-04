# Routing

The Router essentially maps URLs to controller action methods.

## Defining Routes

Routes are defined in the application configuration. An example route might look like this:

    person:
        url: /
        controller: person_controller
        action: showPerson
        methods:
          - GET

When the user hits any URL matching the `url` parameter with a request with the method listed in the `methods` parameter, Veto invokes the action method specified by the `action` parameter on the controller
specified by the `controller` parameter.

Note that the `controller` parameter contains a service locator name, *not* a class path to a controller class. Controllers *must* be registered as Veto services.

Any matched placeholders (Eg. `{name}` in the example above), are passed to the action method as arguments.

In this case, if the URL visited was `/person/Damo`, the `showPerson` method would be passed a single parameter called `$name` containing the string `Damo`.