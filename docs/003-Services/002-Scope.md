
## Scope

Veto takes a simple approach to service scope.

By default, services are persistent - that is to say that once a service class is instantiated, the same instance is used throughout the application.

You can change this behaviour for individual services by providing the `one_shot` parameter in the service definition. If `one_shot` is true for a service, a new instance will be provided each time the service is requested.