<?php

namespace Cms\Themes;

use Cms\Plugins\PluginManager;
use League\Plates\Engine;

class View
{
    private Engine $engine;

    public function __construct(ThemeManager $themes, PluginManager $plugins)
    {
        $this->engine = new Engine($themes->templatesPath());
        $this->engine->addFolder('admin', base_path('core/Admin/Views'));

        foreach ($plugins->discover() as $manifest) {
            if (!$plugins->isActive($manifest->slug)) {
                continue;
            }

            $viewsPath = $manifest->path . '/views';

            if (is_dir($viewsPath)) {
                $this->engine->addFolder($manifest->slug, $viewsPath, true);
            }
        }

        do_action('theme.view.engine', $this->engine);
    }

    public function render(string $name, array $data = []): string
    {
        return $this->engine->render($name, $data);
    }

    public function exists(string $name): bool
    {
        return $this->engine->exists($name);
    }

    public function engine(): Engine
    {
        return $this->engine;
    }
}
