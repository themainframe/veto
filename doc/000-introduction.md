# Introduction

Veto is essentially a collection of classes that work together to help you build and deploy PHP web applications
more quickly.

There are a few components involved:

* **The Front Controller** - Receives web requests and passes them into your Veto application to be handled.

* **The Router** - A layer that decides what action should be taken to handle any given request.

* **A Controller** - Your code goes here! Performs any core functionality in your application. You might use a **model** here to, for instance, interact with a database.

* **The View** - Generally here a templating language *pretties up* the output of your application for presentation to the user.