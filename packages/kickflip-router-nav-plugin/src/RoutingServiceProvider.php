<?php

namespace Kickflip\RouterNavPlugin;

use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContract;
use Illuminate\Http\Request;
use Illuminate\Routing\RoutingServiceProvider as BaseRoutingServiceProvider;
use Illuminate\Routing\UrlGenerator;
use Kickflip\KickflipHelper;

class RoutingServiceProvider extends BaseRoutingServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRouter();
        $this->registerUrlGenerator();
    }

    /**
     * Register the URL generator service.
     *
     * @return void
     */
    protected function registerUrlGenerator()
    {
        /**
         * @var \Illuminate\Contracts\Foundation\Application $app
         */
        $this->app->singleton('url', function ($app) {
            /**
             * @var \Illuminate\Contracts\Foundation\Application $app
             */
            $routes = $app['router']->getRoutes();

            // The URL generator needs the route collection that exists on the router.
            // Keep in mind this is an object, so we're passing by references here
            // and all the registered routes will be available to the generator.
            $app->instance('routes', $routes);

            $_SERVER['HTTP_HOST'] = parse_url(KickflipHelper::config('site.baseUrl'), PHP_URL_HOST);
            $_SERVER['SERVER_PORT'] = '80';
            $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
            return new UrlGenerator(
                $routes, Request::capture(), $app['config']['app.asset_url']
            );
        });

        $this->app->extend('url', function (UrlGeneratorContract $url, $app) {
            // Next we will set a few service resolvers on the URL generator so it can
            // get the information it needs to function. This just provides some of
            // the convenience features to this URL generator like "signed" URLs.
            $url->setSessionResolver(function () {
                return null;
            });

            $url->setKeyResolver(function () {
                return 'base64:'.base64_encode(
                        random_bytes(32)
                    );
            });

            // If the route collection is "rebound", for example, when the routes stay
            // cached for the application, we will need to rebind the routes on the
            // URL generator instance so it has the latest version of the routes.
            $app->rebinding('routes', function ($app, $routes) {
                $app['url']->setRoutes($routes);
            });

            return $url;
        });
    }

}
