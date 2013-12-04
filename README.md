BraincraftedStaticSiteBundle
============================

Static site generator bundle for Symfony2. *Early development release.*

[![Build Status](https://travis-ci.org/braincrafted/static-site-bundle.png?branch=master)](https://travis-ci.org/braincrafted/static-site-bundle)


Table of Contents
-----------------

1. [Motivation](#motivation)
1. [Installation](#installation)
1. [Configuration](#configuration)
    1. [Generators](#generators)
        1. [File Generator](#file-generator)
        1. [Directory Generator](#directory-generator)
        1. [JSON Generator](#json-generator)
        1. [CSV Generator](#csv-generator)
        1. [YAML Generator](#yaml-generator)
1. [Usage](#usage)
1. [Author](#author)
1. [License](#license)


Motivation
----------

The documentation for [BraincraftedBootstrapBundle](https://github.com/braincrafted/bootstrap-bundle) is a Symfony2
project, because it is used to test and demonstrate the bundles features. I no longer wanted to maintain (and pay for)
another Symfony2 project on my server and instead move it to Github Pages. BraincraftedStaticSiteBundle creates static
HTML pages from Symfony2 controllers.


Installation
------------

You can install BraincraftedStaticSiteBundle using [Composer](http://getcomposer.org). Add to your `composer.json`:

```json
{
    "require": {
        "braincrafted/static-site-bundle": "dev-master"
    }
}
```

You also have to add the bundle to your `AppKernel.php`:

```php
// app/AppKernel.php

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Braincrafted\Bundle\StaticSiteBundle\BraincraftedStaticSiteBundle(),
        );

        // ...

        return $bundles;
    }

    // ...
}
```

Configuration
-------------

- `braincrafted_static_site.build_directory`: The directory where the built site is saved.
- `braincrafted_static_site.base_url`: The base URL of the static site. Useful when the HTML is not saved in the root
    directory. Most commands have an option to override this on an individual basis.
- `braincrafted_static_site.index_name`: If the route doesn't contain a file name this is appended to the route.

The default configuration looks like this:

```yaml
# app/config/config.yml
braincrafted_static_site:
    build_directory: "%kernel.root_dir%/../build/site"
    base_url: ''
    index_name: index.html
    generators: ~
```

### Generators

If an action requires parameters you can use generators to load the parameters from various sources. The bundle comes
with four default generators:

- **File generator**: Every line in a file is a parameter; can only be used with actions that require a single parameter
- **Directory generator**: The name of every file or directory in a given directory is a parameter; can only be used with
    actions that require a single parameter
- **JSON generator**: A file that contains an array of objects; each object represents the parameters for an action; can be
    used with actions that require multiple parameters
- **CSV generator**: A file where each row contains the parameters of an action; must contain a header row that includes the
    parameter names; can be used for actions that require multiple parameters

Generators can be configured in your apps `config.yml` on a per-route basis.

#### File Generator

Parameters are generated from a file.

**Required options:**

- `filename`
- `parameter`

In the following example we have a route `acme_demo_page` with the path `/p/{page}` and we want to generate the `page`
parameter from a file called `data.txt`.

```yaml
# app/config/config.yml

braincrafted_static_site:
    generators:
        page:
            route: acme_demo_page
            generator: braincrafted_static_site.file_generator
            options:
                filename: "%kernel.root_dir%/../data.txt"
                parameter: page
```

We require now the `data.txt` file that contains one parameter per line.

    products
    about
    contact

StaticSiteBundle will render the following pages:

- `/p/products`
- `/p/about`
- `/p/contact`

#### Directory Generator

Parameters are generated from the names of files in a directory.

**Required options:**

- `directoryName`
- `parameter`

In this example we have a route `acme_demo_article` with the path `/article/{slug}` and we want to render the page for
every file in a directory.

```yaml
# app/config/config.yml

braincrafted_static_site:
    generators:
        article:
            route: acme_demo_article
            generator: braincrafted_static_site.directory_generator
            options:
                directory_name: "%kernel.root_dir%/../articles"
                parameter: slug
```

The directory `articles/` contains the following files:

    articles/
        ⊢ 2013-12-03-bootstrap-bundle-2-0.md
        ⊢ 2013-12-04-static-site-bundle-0-1.md

StaticSiteBundle will render the following pages:

- `article/2013-12-03-bootstrap-bundle-2-0`
- `article/2013-12-04-static-site-bundle-0-1`

#### JSON Generator

Parameters are generated from a JSON file.

**Required options:**

- `filename`

Let's consider a route `acme_demo_categorypage` with the path `/page/{category}/{page}`. We require two parameters
`category` and `page` that we want to generate from a JSON file called `data.json`.

```yaml
# app/config/config.yml

braincrafted_static_site:
    generators:
        categorypage:
            route: acme_demo_categorypage
            generator: braincrafted_static_site.json_generator
            options:
                filename: "%kernel.root_dir%/../data.json"
```

The JSON file `data.json` has to contain an array where each element is an object with a `category` and a `page`
property:

```json
[
    { "category": "foo", "page": "bar" },
    { "category": "foo", "page": "baz" }
]
```

StaticSiteBundle will render the following pages:

- `/pages/foo/bar`
- `/pages/foo/baz`

#### CSV Generator

Parameters are generated from a CSV file.

**Required options:**

- `filename`

**Optional options:**

- `delimiter` (default value is `,`)
- `enclosure` (default value is `"`)
- `escape` (default value is `\`)

Now we want to render the route `acme_demo_person` with the pattern `/person/{name}/{age}/{city}` using a CSV file
`persons.csv`.

```yaml
# app/config/config.yml

braincrafted_static_site:
    generators:
        person:
            route: acme_demo_person
            generator: braincrafted_static_site.csv_generator
            options:
                filename: "%kernel.root_dir%/../persons.csv"
```

The CSV file has to contain three columns and a header row containing `name`, `age` and `city`.

```csv
"name", "age", "city"
"Florian", "27", "Vienna"
"Daniela", "22", "Vienna"
```

StaticSiteBundle will render the following pages:

- `/person/Florian/27/Vienna`
- `/person/Daniela/22/Vienna`

#### YAML Generator

Parameters are generated from a YAML file.

**Required options:**

- `filename`

If we want to render the route `acme_demo_person` with the pattern `/person/{name}/{age}/{city}` we can also use a YAML
file `persons.yaml`.

```yaml
# app/config/config.yml

braincrafted_static_site:
    generators:
        person:
            route: acme_demo_person
            generator: braincrafted_static_site.yaml_generator
            options:
                filename: "%kernel.root_dir%/../persons.yml"
```

The YAML file `persons.yml` has to contain a list element for every person with a named property for every parameter.

```yml
-
    name: Florian
    age: 27
    city: Vienna
-
    name: Daniela
    age: 22
    city: Vienna
```

StaticSiteBundle will render the following pages:

- `/person/Florian/27/Vienna`
- `/person/Daniela/22/Vienna`


Usage
-----

The build command is the main command offered by BraincraftedStaticSiteBundle. It renders all pages and dumps the assets
into the build directory.

```bash
$ php app/console braincrafted:static-site:build
```

The bundle also contains a set of different commands to build pages. First of all, it is possible to build a single page
based on the name of a controller and an action:

```bash
$ php app/console braincrafted:static-site:render-controller AcmeDemoBundle:Default:index
```

It is also possible to build a page based on the name of its route:

```bash
$ php app/console braincrafted:static-site:render-route acme_demo_default_index
```

You can also render all routes. This command is used internally by the `build` command to render the site, however,
assets are not dumped into the build directory by the `render-routes` command. Internal routes of Symfony2 (starting
with `_`) are excluded:

```bash
$ php app/console braincrafted:static-site:render-routes
```

The HTML code will be saved in the directory configured with `braincrafted_static_site.build_directory`.

_**Note:** BraincraftedStaticSiteBundle can handle actions with parameters when a generator is configured for these
routes._

When you call one of these commands BraincraftedStaticSiteBundle uses the Symfony2 kernel to simulate a request to a
page. The kernel is booted in the same environment as the command. If you want to build the pages for production, you
need to build them in the <code>prod</code> environment.

```bash
$ php app/console braincrafted:static-site:build -e prod
```

If <code>braincrafted:static-site:build</code> is called in the prod environment the cache is cleared before the
rendering.


Author
------

- [Florian Eckerstorfer](http://florian.ec) ([Twitter](http://twitter.com/Florian_), [App.net](http://app.net/florian))


License
-------

This bundle is licensed under the [MIT license](http://opensource.org/licenses/MIT). For more information see the
LICENSE file.
