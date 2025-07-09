<p align="center">
    <a href="https://www.3brs.com" target="_blank">
        <img src="https://3brs1.fra1.cdn.digitaloceanspaces.com/3brs/logo/3BRS-logo-sylius-200.png"/>
    </a>
</p>
<h1 align="center">
MailChimp Plugin
<br />
    <a href="https://packagist.org/packages/3brs/sylius-mailchimp-plugin" title="License" target="_blank">
        <img src="https://img.shields.io/packagist/l/3brs/sylius-mailchimp-plugin.svg" />
    </a>
    <a href="https://packagist.org/packages/3brs/sylius-mailchimp-plugin" title="Version" target="_blank">
        <img src="https://img.shields.io/packagist/v/3brs/sylius-mailchimp-plugin.svg" />
    </a>
    <a href="https://circleci.com/gh/3BRS/sylius-mailchimp-plugin" title="Build status" target="_blank">
        <img src="https://circleci.com/gh/3BRS/sylius-mailchimp-plugin.svg?style=shield" />
    </a>
</h1>

## Features

* Per channel configurable options
* Subscribe user during checkout
* Subscribe user during registration
* Sync newsletter preferences in customer's profile
* Select the mailing list per channel
* Configure double opt-in per channel
* This plugin, unlike others, can handle large mailing lists

<p align="center">
    <img src="https://github.com/3BRS/sylius-mailchimp-plugin/blob/master/doc/admin.png?raw=true"/>
</p>

## Installation

1. Run `$ composer require 3brs/sylius-mailchimp-plugin`.
2. Register `\ThreeBRS\SyliusMailChimpPlugin\ThreeBRSSyliusMailChimpPlugin` in your Kernel.
3. Your Entity `Channel` has to implement `\ThreeBRS\SyliusMailChimpPlugin\Entity\ChannelMailChimpSettingsInterface`. You can use Trait `ThreeBRS\SyliusMailChimpPlugin\Entity\ChannelMailChimpSettingsTrait`. 
4. Add config to `config/packages/_sylius.yaml`
   ```yaml
   imports:
        ...
            - { resource: "@ThreeBRSSyliusMailChimpPlugin/config/config.yaml" }
   ```
5. Create and run doctrine database migrations.

For guide to use your own entity see [Sylius docs - Customizing Models](https://old-docs.sylius.com/en/1.13/customization/model.html).

## Configuration

Set the API Key in `parameters.yml`

```
three_brs_sylius_mail_chimp:
    mailchimp_api_key: API_KEY
```

## Optional (subscription from checkout)

- Include subscribe checkbox template into checkout `{{ include('@ThreeBRSSyliusMailChimpPlugin/newsletterSubscribeForm.html.twig') }}` 

## Development

### Usage

- Create symlink from .env.dist to .env or create your own .env file
- Develop your plugin in `/src`
- See `bin/` for useful commands

### Testing

After your changes you must ensure that the tests are still passing.

```bash
$ composer install
$ bin/console doctrine:schema:create -e test
$ bin/phpstan.sh
$ bin/ecs.sh
```

License
-------
This library is under the MIT license.

Credits
-------
Developed by [3BRS](https://3brs.com)<br>
Forked from [manGoweb](https://github.com/mangoweb-sylius/SyliusPaymentFeePlugin).
