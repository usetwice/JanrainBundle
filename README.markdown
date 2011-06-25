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

Requirements
------------

1. Use FOSUserBundle.
2. If you are using the Symfony2 vendors method, add this to your deps file:

    [EvarioJanrainBundle]
        git=git://github.com/evario/JanrainBundle.git
        target=/bundles/Evario/JanrainBundle

    Then run bin/vendors install

3. Add the Evario namespace to your autoloader:

    // app/autoload.php
    $loader->registerNamespaces(array(
        'Evario' => __DIR__.'/../vendor/bundles',
        // your other namespaces
    );

4. Add JanrainBundle to your application kernel

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

5. Add janrainId string column to user table.
6. Add the provider service:

    services:
        evario.janrain.user:
            class: Evario\JanrainBundle\Security\User\Provider\JanrainProvider
            arguments:
                userManager: "@fos_user.user_manager"
                validator: "@validator"
                apiKey: %evario_janrain.options.api_key%
                container: "@service_container"

7. Update your security.yml file to use the new user provider.

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

8. Set the parameters in your config.yml file:

    # app/config/config.yml
    evario_janrain:
        api_key: ~ # your janrain api key