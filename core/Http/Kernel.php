<?php

namespace Cms\Http;

use Cms\Application;

class Kernel
{
    public function __construct(private Application $app, private Router $router)
    {
    }

    public function handle(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $request = Request::capture();

        if (!$this->app->isInstalled() && !str_starts_with($request->path, '/install')) {
            Response::redirect('/install')->send();

            return;
        }

        do_action('cms.boot');

        $response = $this->router->dispatch($request);
        $response->send();
    }
}
