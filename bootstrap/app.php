<?php

require_once __DIR__.'/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__.'/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    realpath(__DIR__.'/../')
);

 $app->withFacades();
 class_alias('Illuminate\Support\Facades\Storage', 'Storage');
//
// class_alias('Tymon\JWTAuth\Facades\JWTAuth', 'JWTAuth');
///** This gives you finer control over the payloads you create if you require it.
// *  Source: https://github.com/tymondesigns/jwt-auth/wiki/Installation
// */
// class_alias('Tymon\JWTAuth\Facades\JWTFactory', 'JWTFactory'); // Optional
//$app->alias('cache', 'Illuminate\Cache\CacheManager');
//$app->alias('auth', 'Illuminate\Auth\AuthManager');

 $app->withEloquent();
 $app->configure('jwt');
 $app->configure('filesystems');
 $app->configure('wechat');
 $app->configure('dingding');
 $app->configure('facepp');
// $app['Dingo\Api\Auth\Auth']->extend('oauth', function ($app) {
//    return new Dingo\Api\Auth\Provider\JWT($app['Tymon\JWTAuth\JWTAuth']);
//});
/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

// $app->middleware([
//     Illuminate\Session\Middleware\StartSession::class
// ]);

// $app->routeMiddleware([
//     'actionfilter' => App\Http\Middleware\ActionFilterMiddleware::class,
// ]);

 $app->routeMiddleware([
    'jwt.auth'    => Tymon\JWTAuth\Http\Middleware\Authenticate::class,
     'jwt.unauth'    => Tymon\JWTAuth\Http\Middleware\UnAuthenticate::class,
    'jwt.refresh' => Tymon\JWTAuth\Http\Middleware\RefreshToken::class,
     'auth' => App\Http\Middleware\Authenticate::class,
     'cors' => App\Http\Middleware\LumenCors::class,
     'throttle' => App\Http\Middleware\ThrottleRequests::class,
 ]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/
 $app->register(Illuminate\Filesystem\FilesystemServiceProvider::class);
 $app->register(Maatwebsite\Excel\ExcelServiceProvider::class);
 $app->register(App\Providers\AppServiceProvider::class);
 $app->register(App\Providers\AuthServiceProvider::class);
 $app->register(Dingo\Api\Provider\LumenServiceProvider::class);
 $app->register(Tymon\JWTAuth\Providers\LumenServiceProvider::class);
 app('Dingo\Api\Auth\Auth')->extend('jwt', function ($app) {
    return new Dingo\Api\Auth\Provider\JWT($app['Tymon\JWTAuth\JWTAuth']);
 });
//Injecting auth
$app->singleton(Illuminate\Auth\AuthManager::class, function ($app) {
    return $app->make('auth');
});
// $app->register(App\Providers\EventServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->group(['namespace' => 'App\Http\Controllers'], function ($app) {
    require __DIR__.'/../routes/web.php';
    require __DIR__.'/../routes/api.php';
});

$app->configureMonologUsing(function(Monolog\Logger $monoLog) use ($app){
    return $monoLog->pushHandler(
        new \Monolog\Handler\RotatingFileHandler($app->storagePath().'/logs/lumen.log',5)
    );
});

return $app;
