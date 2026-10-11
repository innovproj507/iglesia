<?php

namespace Cms\Menus;

use Cms\Database\Connection;

class MenuRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function findByLocation(string $location): ?array
    {
        return $this->connection->medoo()->get('menus', '*', ['location' => $location]) ?: null;
    }

    public function all(): array
    {
        return $this->connection->medoo()->select('menus', '*');
    }

    public function create(string $name, ?string $location = null): int
    {
        return (int) $this->connection->insert('menus', ['name' => $name, 'location' => $location]);
    }

    public function items(int $menuId): array
    {
        $items = $this->connection->medoo()->select('menu_items', '*', [
            'menu_id' => $menuId,
            'ORDER' => ['sort_order' => 'ASC'],
        ]);

        return $this->buildTree($items);
    }

    public function addItem(int $menuId, string $label, ?string $url, ?int $postId = null, ?int $parentId = null, int $sortOrder = 0): int
    {
        return (int) $this->connection->insert('menu_items', [
            'menu_id' => $menuId,
            'parent_id' => $parentId,
            'label' => $label,
            'url' => $url,
            'post_id' => $postId,
            'target' => '_self',
            'sort_order' => $sortOrder,
        ]);
    }

    public function removeItem(int $itemId): void
    {
        $this->connection->delete('menu_items', ['id' => $itemId]);
    }

    private function buildTree(array $items, ?int $parentId = null): array
    {
        $branch = [];

        foreach ($items as $item) {
            if ((int) ($item['parent_id'] ?? 0) === (int) $parentId || ($parentId === null && $item['parent_id'] === null)) {
                $item['children'] = $this->buildTree($items, (int) $item['id']);
                $branch[] = $item;
            }
        }

        return $branch;
    }
}
