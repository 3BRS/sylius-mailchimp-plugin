# UPGRADE FROM 1.1.0 to 2.0.0

## File Location Changes

* Moved config/ dir. out of src/ and into plugin root dir.
* Moved templates/ dir. out of src/ and into plugin root dir.
* Moved translations/ dir. out of src/ and into plugin root dir.

## Config Changes 

* The main config file is now located at: `config/config.yml`

## Twig Hooks

Templates are now rendered using Twig hooks, which is the standard in Sylius 2:

* **Admin**

    * [admin_hooks.yaml](https://github.com/3BRS/sylius-mailchimp-plugin/blob/master/config/app/twig_hooks/admin_hooks.yaml) contains config for Twig hooks used in Admin

* **Shop**

    * [shop_hooks.yaml](https://github.com/3BRS/sylius-mailchimp-plugin/blob/master/config/app/twig_hooks/shop_hooks.yaml) contains config for Twig hooks used in Shop
