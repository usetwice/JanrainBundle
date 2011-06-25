**Caution:** This bundles is developed in sync with [symfony's repository](https://github.com/symfony/symfony)

Documentation
-------------

The documentation is not yet written.

About
-----

JanrainBundle integrates Janrain into Symfony2 projects. It is very much a work in progress.

TODO
----

Make it work...
Integrate social sharing.
Integrate inviting/referring friends.

Configuration
------------

Use FOSUserBundle
~~~~~~~~~~~~~~~~~

Fully implement the FOSUserBundle...

Add EvarioJanrainBundle to your vendor/bundles/ dir
---------------------------------------------

::

    [EvarioJanrainBundle]
        git=git://github.com/evario/JanrainBundle.git
        target=/bundles/Evario/JanrainBundle

    Then run bin/vendors install

Add the Evarop namespace to your autoloader
-------------------------------------------

::

    // app/autoload.php
    $loader->registerNamespaces(array(
        'Evario' => __DIR__.'/../vendor/bundles',
        // your other namespaces
    );

Add JanrainBundle to your application kernel
--------------------------------------------

::

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            // ...
            new FOS\UserBundle\FOSUserBundle(),
            // ...
            new Evario\JanrainBundle\EvarioJanrainBundle(),
            // ...
        );
    }

Add janrainId string column to user table
-----------------------------------------

Add the provider service
------------------------

::

    services:
        evario.janrain.user:
            class: Evario\JanrainBundle\Security\User\Provider\JanrainProvider
            arguments:
                userManager: "@fos_user.user_manager"
                validator: "@validator"
                apiKey: %evario_janrain.options.api_key%
                container: "@service_container"

Update your security.yml file to use the new user provider
----------------------------------------------------------

::

    factories:
        - "%kernel.root_dir%/../vendor/bundles/Evario/JanrainBundle/Resources/config/security_factories.xml"

    providers:
        evario_janrain:
            id: evario.janrain.user

    firewalls:
        main:
            pattern:      .*
            form_login:
                provider:       evario_janrain
                login_path:     /login
                use_forward:    false
                check_path:     /login_check
                failure_path:   null
            logout:       true
            anonymous:    true

Set the parameters in your config.yml file
------------------------------------------

::

    # app/config/config.yml
    evario_janrain:
        api_key: ~ # your janrain api key