<?php namespace TheDMSGrp\Cloudflare;

use Backend;
use System\Classes\PluginBase;

/**
 * https Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Cloudflare',
            'description' => 'Puts October in HTTPS protocol correctly when behind the cloudflare reverse proxy.',
            'author'      => 'TheDMSGrp',
            'icon'        => 'icon-leaf'
        ];
    }

}
