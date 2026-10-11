<?php

namespace Database\Seeders;

use Cms\Database\Connection;

class RolesAndOptionsSeeder
{
    private const CAPABILITIES = [
        'manage_options', 'manage_plugins', 'manage_themes', 'manage_users',
        'manage_media', 'manage_menus', 'edit_posts', 'edit_others_posts',
        'publish_posts', 'upload_files',
    ];

    private const ROLE_CAPABILITIES = [
        'admin' => self::CAPABILITIES,
        'editor' => ['edit_posts', 'edit_others_posts', 'publish_posts', 'manage_media', 'manage_menus'],
        'author' => ['edit_posts', 'upload_files'],
    ];

    public function __construct(private Connection $connection)
    {
    }

    public function run(): void
    {
        $capabilityIds = [];

        foreach (self::CAPABILITIES as $slug) {
            $capabilityIds[$slug] = $this->connection->insert('capabilities', [
                'slug' => $slug,
                'name' => ucwords(str_replace('_', ' ', $slug)),
            ]);
        }

        foreach (self::ROLE_CAPABILITIES as $roleSlug => $capabilities) {
            $roleId = $this->connection->insert('roles', [
                'slug' => $roleSlug,
                'name' => ucfirst($roleSlug),
            ]);

            foreach ($capabilities as $capabilitySlug) {
                $this->connection->insert('role_capabilities', [
                    'role_id' => $roleId,
                    'capability_id' => $capabilityIds[$capabilitySlug],
                ]);
            }
        }

        $this->connection->insert('options', ['option_name' => 'site_title', 'option_value' => 'Mi Iglesia', 'autoload' => 1]);
        $this->connection->insert('options', ['option_name' => 'tagline', 'option_value' => 'Bienvenidos a nuestra comunidad', 'autoload' => 1]);
        $this->connection->insert('options', ['option_name' => 'active_theme', 'option_value' => 'iglesia-base', 'autoload' => 1]);
        $this->connection->insert('options', ['option_name' => 'service_times', 'option_value' => json_encode(['Domingos 9:00 AM & 11:45 AM', 'Miércoles 8:00 PM']), 'autoload' => 1]);
        $this->connection->insert('options', ['option_name' => 'social_links', 'option_value' => json_encode([]), 'autoload' => 1]);

        $menuId = $this->connection->insert('menus', ['name' => 'Principal', 'location' => 'primary']);
        $this->connection->insert('menus', ['name' => 'Pie de página', 'location' => 'footer']);
        $this->connection->insert('menu_items', ['menu_id' => $menuId, 'label' => 'Inicio', 'url' => '/', 'target' => '_self', 'sort_order' => 0]);
    }

    public function roleIdFor(string $slug): int
    {
        return (int) $this->connection->medoo()->get('roles', 'id', ['slug' => $slug]);
    }
}
