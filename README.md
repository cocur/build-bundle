BcStaticSiteBundle
==================

Bundle to render static sites from Symfony2. *Early development release.*


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

Currently this bundle contains one command to render a page to the `build_directory` based on its controller name.

    php app/console bc:static-site:render-controller AcmeDemoBundle:Default:index

The HTML code will be saved in the directory configured with `bc_static_site.build_directory`.

_**Note:** BcStaticSiteBundle currently only handles controllers without parameters._

License
-------

This bundle is licensed under the [MIT license](http://opensource.org/licenses/MIT). For more information see the LICENSE file.
