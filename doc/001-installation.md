## Installation

If you're starting a new project from scratch, we suggest you use the Veto Skeleton project. It contains sample configuration files, a sample controller & a general-use front controller to get you up and running in no time. The Skeleton project also pulls in [Twig](http://twig.sensiolabs.org/), a powerful templating engine.

You can use [Composer](https://getcomposer.org/) to grab and install Veto quickly:

    curl -sS https://getcomposer.org/installer | php

Once you have Composer, you can easilly install Veto along with the Skeleton package:

    php composer.phar create-project themainframe/veto-skeleton -s dev myproject


Composer will now create a new directory called `myproject` then download and set up the Skeleton project package for you.

You should set up your web server to use `/web` as the document root for the application. Any public assets or resources must be inside the `/web` directory.
