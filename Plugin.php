<?php namespace Winter\Cloudflare;

use App;
use Config;
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
     */
    public function pluginDetails(): array
    {
        return [
            'name'        => 'winter.cloudflare::lang.plugin.name',
            'description' => 'winter.cloudflare::lang.plugin.description ',
            'author'      => 'Winter CMS',
            'icon'        => 'icon-cloudflare',
            'homepage'    => 'https://github.com/wintercms/wn-cloudflare-plugin',
            'replaces'    => ['HeathDutton.Cloudflare' => '<= 1.0.3'],
        ];
    }

    /**
     * Boot the plugin
     */
    public function boot()
    {
        $this->disableRocketLoaderOnBackendAssets();
        $this->trustCloudflareProxies();
        $this->fixClientIpDetection();
    }

    /**
     * Disable Cloudflare's Rocket Loader on backend assets
     */
    protected function disableRocketLoaderOnBackendAssets()
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

    /**
     * Trust Cloudflare's proxies
     */
    protected function trustCloudflareProxies()
    {
        $request = request();
        $trustedProxies = Config::get('app.trustedProxies', null);

        // All proxies already trusted, exit early
        if ($trustedProxies === '*' || $trustedProxies === '**') {
            return;
        }

        if (is_null($trustedProxies)) {
            $trustedProxies = [];
        }

        // Trust localhost for backward compatibility.
        $trustedProxies = array_merge($trustedProxies, [
            '127.0.0.1',
        ]);

        // CloudFlare proxy list.
        // This rarely changes but can be found here: https://www.cloudflare.com/ips/
        $trustedProxies += [
            // Cloudflare IPv4 list
            '103.21.244.0/22',
            '103.22.200.0/22',
            '103.31.4.0/22',
            '104.16.0.0/13',
            '104.24.0.0/14',
            '108.162.192.0/18',
            '131.0.72.0/22',
            '141.101.64.0/18',
            '162.158.0.0/15',
            '172.64.0.0/13',
            '173.245.48.0/20',
            '188.114.96.0/20',
            '190.93.240.0/20',
            '197.234.240.0/22',
            '198.41.128.0/17',

            // Cloudflare IPv6 list
            '2400:cb00::/32',
            '2606:4700::/32',
            '2803:f800::/32',
            '2405:b500::/32',
            '2405:8100::/32',
            '2a06:98c0::/29',
            '2c0f:f248::/32',
        ];

        // Trust traffic from a local network machine or load-balancer.
        if (!filter_var($_SERVER['REMOTE_ADDR'] ?? '', FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
            $trustedProxies += [
                $request->getClientIp()
            ];
        }

        // Set the trusted proxies value
        Config::set('app.trustedProxies', $trustedProxies);

        // Enforce https schema on rendering to match the proxy closest to us:
        $ssl = false;
        if (
            !empty($_SERVER['HTTP_X_FORWARDED_PROTO'])
            && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https'
        ) {
            // Generic reverse proxy over SSL.
            $ssl = true;
        } elseif (!empty($_SERVER['HTTP_CF_VISITOR'])) {
            // Cloudflare reverse proxy over SSL.
            $visitor = json_decode($_SERVER['HTTP_CF_VISITOR']);
            if ($visitor->scheme == 'https') {
                $ssl = true;
            }
        }
        if ($ssl) {
            if (method_exists($this->app['url'], 'forceSchema')) {
                $this->app['url']->forceSchema('https');
            } else if (method_exists($this->app['url'], 'forceScheme')) {
                $this->app['url']->forceScheme('https');
            }
        }
    }

    /**
     * Fix client IP detection when behind Cloudflare's proxies
     */
    protected function fixClientIpDetection()
    {
        $request = request();

        // Correct IP detection using Cloudflare, even when behind a load balancer.
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
            $request->server->set('REMOTE_ADDR', $_SERVER['HTTP_CF_CONNECTING_IP']);
        }
    }
}
