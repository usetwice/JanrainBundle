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

1. The FOSUserBundle.
2. Add janrainId string column to user table.
3. Add the provider service:

    services:
        evario.janrain.user:
            class: Evario\JanrainBundle\Security\User\Provider\JanrainProvider
            arguments:
                userManager: "@fos_user.user_manager"
                validator: "@validator"
                options: []
                container: "@service_container"

4. Update your security.yml file to use the new user provider.

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