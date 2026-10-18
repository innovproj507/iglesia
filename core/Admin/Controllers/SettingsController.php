<?php

namespace Cms\Admin\Controllers;

use Cms\Admin\AdminMenu;
use Cms\Auth\AuthManager;
use Cms\Http\Request;
use Cms\Http\Response;
use Cms\Settings\OptionsRepository;
use Cms\Themes\View;

class SettingsController extends AdminController
{
    public function __construct(
        View $view,
        AuthManager $auth,
        AdminMenu $adminMenu,
        private OptionsRepository $options,
    ) {
        parent::__construct($view, $auth, $adminMenu);
    }

    public function index(Request $request): Response
    {
        return $this->render('settings/index', [
            'title' => 'Ajustes',
            'siteTitle' => $this->options->get('site_title', ''),
            'tagline' => $this->options->get('tagline', ''),
            'serviceTimes' => option_array('service_times'),
            'socialLinks' => option_array('social_links'),
            'liveUrl' => $this->options->get('live_url', ''),
            'heroImage' => $this->options->get('hero_image', ''),
            'welcomeText' => $this->options->get('welcome_text', ''),
            'address' => $this->options->get('address', ''),
            'logo' => $this->options->get('logo', ''),
            'phone' => $this->options->get('phone', ''),
            'email' => $this->options->get('email', ''),
            'whatsapp' => (string) $this->options->get('whatsapp', ''),
        ], 'settings');
    }

    public function update(Request $request): Response
    {
        $this->options->set('site_title', (string) $request->input('site_title', ''));
        $this->options->set('tagline', (string) $request->input('tagline', ''));

        foreach (['live_url', 'hero_image', 'welcome_text', 'address', 'logo', 'phone', 'email', 'whatsapp'] as $key) {
            $this->options->set($key, trim((string) $request->input($key, '')));
        }

        $serviceTimes = array_filter(array_map('trim', explode("\n", (string) $request->input('service_times', ''))));
        $this->options->set('service_times', array_values($serviceTimes));

        $socialLinks = [
            'facebook' => (string) $request->input('facebook', ''),
            'instagram' => (string) $request->input('instagram', ''),
            'youtube' => (string) $request->input('youtube', ''),
            'x' => (string) $request->input('x', ''),
        ];
        $this->options->set('social_links', array_filter($socialLinks));

        return Response::redirect('/admin/settings');
    }
}
