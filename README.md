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
    <a href="http://travis-ci.org/3brs/SyliusMailChimpPlugin" title="Build status" target="_blank">
        <img src="https://img.shields.io/travis/3brs/SyliusMailChimpPlugin/master.svg" />
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
	<img src="https://raw.githubusercontent.com/3brs/sylius-mailChimp-plugin/master/doc/admin.png"/>
</p>

## Installation

1. Run `$ composer require 3brs/sylius-mailchimp-plugin`.
2. Register `\MangoSylius\MailChimpPlugin\MangoSyliusMailChimpPlugin` in your Kernel.
3. Your Entity `Channel` has to implement `\MangoSylius\MailChimpPlugin\Entity\ChannelMailChimpSettingsInterface`. You can use Trait `MangoSylius\MailChimpPlugin\Entity\ChannelMailChimpSettingsTrait`. 
4. Include `{{ include('@MangoSyliusMailChimpPlugin/mailChimpChannelSettingsForm.html.twig') }}` in channel edit page.

For guide to use your own entity see [Sylius docs - Customizing Models](https://docs.sylius.com/en/1.3/customization/model.html).

## Configuration

Set the API Key in `parameters.yml`

```
mango_sylius_mail_chimp:
	mailchimp_api_key: API_KEY
```

## Optional (subscription from checkout)

- Include subscribe checkbox template into checkout `{{ include('@MangoSyliusMailChimpPlugin/newsletterSubscribeForm.html.twig') }}` 

## Development

### Usage

- Create symlink from .env.dist to .env or create your own .env file
- Develop your plugin in `/src`
- See `bin/` for useful commands

### Testing

After your changes you must ensure that the tests are still passing.
* Easy Coding Standard
  ```bash
  bin/ecs.sh
  ```
* PHPStan
  ```bash
  bin/phpstan.sh
  ```
License
-------
This library is under the MIT license.

Credits
-------
Developed by [3BRS](https://3brs.com)<br>
Forked from [manGoweb](https://github.com/mangoweb-sylius/SyliusPaymentFeePlugin).
