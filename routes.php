<?php

App::before(function ($request) {

    // Trust localhost for backward compatibility.
    $trusted_proxies = [
        '127.0.0.1',
    ];

    // CloudFlare proxy IPv4 list. This rarely changes but can be found here: https://www.cloudflare.com/ips/
    $trusted_proxies += [
        '103.21.244.0/22',
        '103.22.200.0/22',
        '103.31.4.0/22',
        '104.16.0.0/12',
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
    ];

    // Trust traffic from a local network machine or load-balancer.
    if (!filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
        $trusted_proxies += [
            $request->getClientIp()
        ];
    }

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

    // Correct IP detection using Cloudflare, even when behind a load balancer.
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
        $request->server->set('REMOTE_ADDR', $_SERVER['HTTP_CF_CONNECTING_IP']);
    }

    $request->setTrustedProxies($trusted_proxies, \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR);
});
