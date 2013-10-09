BcStaticSiteBundle
==================

Bundle to render static sites from Symfony2. *Early development release.*

[![Build Status](https://travis-ci.org/braincrafted/static-site-bundle.png?branch=master)](https://travis-ci.org/braincrafted/static-site-bundle)


Author
------

- [Florian Eckerstorfer](http://florian.ec)


Motivation
----------

The documentation for [BcBootstrapBundle](https://github.com/braincrafted/bootstrap-bundle) is a Symfony2 project, since it is also used to test and demonstrate the bundles features. However, I no longer wanted to maintain (and pay for) another Symfony2 project on my server and instead move it to Github Pages. BcStaticSiteBundle creates static HTML pages from Symfony2 controllers, but currently not more.


Installation
------------

You can install BcStaticSiteBundle using [Composer](http://getcomposer.org). Add to your `composer.json`

    {
        "requiere": {
            "braincrafted/static-site-bundle": "dev-master"
        }
    }

Of course, you also need to add the bundle to your `AppKernel.php`:

    // app/AppKernel.php

    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                // ...
                new Bc\Bundle\StaticSiteBundle\BcStaticSiteBundle(),
            );

            // ...

            return $bundles;
        }

        // ...
    }


Currently there is one configuration option, `bc_static_site.build_directory`.

    # app/config/config.yml
    bc_static_site:
        build_directory: %kernel.root_dir%/../build/site


Usage
-----

The bundle contains a set of different commands to build pages. First of all, it is possible to build a single page based on the name of the controller:

    php app/console bc:static-site:render-controller AcmeDemoBundle:Default:index

It is also possible to build a page based on the name of its route:

    php app/console bc:static-site:render-route acme_demo_default_index

If you want to build all pages where a route exists, this is also possible. Internal routes of Symfony2 (starting with `_`) are excluded:

    php app/console bc:static-site:render-routes

The HTML code will be saved in the directory configured with `bc_static_site.build_directory`.

_**Note:** BcStaticSiteBundle currently only handles controllers without parameters._

License
-------

This bundle is licensed under the [MIT license](http://opensource.org/licenses/MIT). For more information see the LICENSE file.
