# Configuration

Configuration is a fundamental part of setting up a new Veto-based application. Natively, Veto handles [YAML](http://yaml.org/) format
configuration data normally read from a single file, though multiple files may be used (see below).

Veto reads configuration data in distinct keys within the YAML data:

* `parameters` - Configures constant values for your application.
* `services` - Configures the Dependency Injection mechanism employed by Veto.
* `layers` - Configures the layers that make up your application.
* `routes` - Configures the routes (or *endpoints* of your application).
