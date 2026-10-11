<?php

namespace Cms\Content;

class TaxonomyRegistry
{
    /** @var array<string, array{label: string, post_types: string[]}> */
    private array $taxonomies = [];

    public function register(string $slug, string $label, array $postTypes = []): void
    {
        $this->taxonomies[$slug] = ['label' => $label, 'post_types' => $postTypes];
    }

    public function get(string $slug): ?array
    {
        return $this->taxonomies[$slug] ?? null;
    }

    public function all(): array
    {
        return $this->taxonomies;
    }
}
