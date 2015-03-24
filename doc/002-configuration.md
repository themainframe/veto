# Configuration

Configuration is a fundamental part of setting up a new Veto-based application. Natively, Veto reads [YAML format](http://www.yaml.org/spec/1.2/spec.html) configuration data.

Veto reads configuration data in three distinct keys within the YAML data:

* `services` - Configures the Dependency Injection mechanism employed by Veto.
* `layers` - Configures the layers that make up your application.
* `routes` - Configures the routes (or *endpoints* of your application).

## Loading Configuration

You can have Veto load a configuration file at any time where you have the `$app` object in scope:

    $app->loadConfig('path/to/file.yml');