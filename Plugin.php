<?php namespace HeathDutton\Cloudflare;

use App;
use Event;
use System\Classes\PluginBase;

/**
 * Plugin Information File
 */
class Plugin extends PluginBase
{
    public $elevated = true;

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
    
    /**
     * Boot the plugin
     */
    public function boot()
    {
        // Prevent CloudFlare's Rocket Loader from breaking scripts in the backend
        // @see octobercms/october#4611, octobercms/october#4092, octobercms/october#3841, octobercms/october#3839
        if (App::runningInBackend()) {
            Event::listen('system.assets.beforeAddAsset', function ($type, $path, &$attributes) {
                if ($type === 'js') {
                    $attributes['data-cfasync'] = 'false';
                }
            });
        }
    }
}
