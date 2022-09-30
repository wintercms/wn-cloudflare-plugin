# Cloudflare for Winter CMS

![Cloudflare Plugin](https://user-images.githubusercontent.com/15900351/193173187-451bca1a-f484-4c41-b5d1-f01e49d2c765.png)

*Ensure that HTTPS and IP detection works flawlessly with Cloudflare and Winter CMS*

This super-simple plugin ensures compatibility with Cloudflare Flexible and Full SSL, even when behind a load balancer.

## Installation

Before using Cloudflare Flexbile SSL, we reccomend you remove any http -> https redirections in your .htaccess that you may have added. We find it safer to make this kind of redirection as a page rule in Cloudflare. Install this plugin and thats it!

This plugin is available for installation via [Composer](http://getcomposer.org/).

```bash
composer require winter/wn-cloudflare-plugin
```

No configuration is necessary.

## License

This package is licensed under the [MIT license](https://github.com/wintercms/wn-cloudflare-plugin/blob/master/LICENSE.txt).

## Credits
This plugin was originally written by Heath Dutton: https://github.com/heathdutton/cloudflare

It has since been modified and re-released under the Winter namespace as a first party plugin for Winter CMS maintained by the Winter CMS team.

If you would like to contribute to this plugin's development, please feel free to submit issues or pull requests to the plugin's repository here: https://github.com/wintercms/wn-cloudflare-plugin

If you would like to support Winter CMS, please visit [WinterCMS.com](https://wintercms.com/support)

*Cloudflare and the Cloudflare logo are trademarks and/or registered trademarks of Cloudflare, Inc. in the United States and other jurisdictions.*
