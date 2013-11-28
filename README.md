BraincraftedStaticSiteBundle
==================

Bundle to render static sites from Symfony2. *Early development release.*

[![Build Status](https://travis-ci.org/braincrafted/static-site-bundle.png?branch=master)](https://travis-ci.org/braincrafted/static-site-bundle)


Author
------

- [Florian Eckerstorfer](http://florian.ec)


Motivation
----------

The documentation for [BraincraftedBootstrapBundle](https://github.com/braincrafted/bootstrap-bundle) is a Symfony2 project, because it is used to test and demonstrate the bundles features. I no longer wanted to maintain (and pay for) another Symfony2 project on my server and instead move it to Github Pages. BraincraftedStaticSiteBundle creates static HTML pages from Symfony2 controllers.


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
- `braincrafted_static_site.base_url`: The base URL of the static site. Useful when the HTML is not saved in the root directory. Most commands have an option to override this on an individual basis.
- `braincrafted_static_site.index_name`: If the route doesn't contain a file name this is appended to the route.

The default configuration looks like this:

```yaml
# app/config/config.yml
braincrafted_static_site:
    build_directory: %kernel.root_dir%/../build/site
    base_url: ''
    index_name: index.html
```


Usage
-----

The build command is the main command offered by BraincraftedStaticSiteBundle. It builds all pages and dumps the assets into the build directory.

    php app/console braincrafted:static-site:build

The bundle also contains a set of different commands to build pages. First of all, it is possible to build a single page based on the name of the controller:

    php app/console braincrafted:static-site:render-controller AcmeDemoBundle:Default:index

It is also possible to build a page based on the name of its route:

    php app/console braincrafted:static-site:render-route acme_demo_default_index

If you want to build all pages where a route exists, this is also possible. Internal routes of Symfony2 (starting with `_`) are excluded:

    php app/console braincrafted:static-site:render-routes

The HTML code will be saved in the directory configured with `braincrafted_static_site.build_directory`.

_**Note:** BraincraftedStaticSiteBundle currently only handles controllers without parameters._

When you call one of these commands BraincraftedStaticSiteBundle uses the Symfony2 kernel to simulate a request to a page. The kernel is booted in the same environment as the command. If you want to build the pages for production, you need to build them in the <code>prod</code> environment.

    php app/console braincrafted:static-site:build -e prod

If <code>braincrafted:static-site:build</code> is called in the prod environment the cache is cleared before the rendering.

License
-------

This bundle is licensed under the [MIT license](http://opensource.org/licenses/MIT). For more information see the LICENSE file.
