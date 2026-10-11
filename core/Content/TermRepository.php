<?php

namespace Cms\Content;

use Cms\Database\Connection;

class TermRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function findOrCreate(string $taxonomy, string $name, ?string $slug = null): array
    {
        $slug = $slug ?? $this->slugify($name);
        $existing = $this->connection->medoo()->get('terms', '*', ['taxonomy' => $taxonomy, 'slug' => $slug]);

        if ($existing) {
            return $existing;
        }

        $id = $this->connection->insert('terms', ['taxonomy' => $taxonomy, 'name' => $name, 'slug' => $slug]);

        return ['id' => $id, 'taxonomy' => $taxonomy, 'name' => $name, 'slug' => $slug];
    }

    public function findBySlug(string $taxonomy, string $slug): ?array
    {
        return $this->connection->medoo()->get('terms', '*', ['taxonomy' => $taxonomy, 'slug' => $slug]) ?: null;
    }

    public function all(string $taxonomy): array
    {
        return $this->connection->medoo()->select('terms', '*', ['taxonomy' => $taxonomy]);
    }

    public function attach(int $postId, int $termId): void
    {
        $exists = $this->connection->medoo()->has('term_relationships', ['post_id' => $postId, 'term_id' => $termId]);

        if (!$exists) {
            $this->connection->insert('term_relationships', ['post_id' => $postId, 'term_id' => $termId]);
        }
    }

    public function termsForPost(int $postId, string $taxonomy): array
    {
        return $this->connection->medoo()->select('term_relationships', [
            '[><]terms' => ['term_id' => 'id'],
        ], ['terms.id', 'terms.name', 'terms.slug'], [
            'term_relationships.post_id' => $postId,
            'terms.taxonomy' => $taxonomy,
        ]);
    }

    public function postsForTerm(int $termId): array
    {
        return $this->connection->medoo()->select('term_relationships', 'post_id', ['term_id' => $termId]);
    }

    private function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? $value;

        return trim($value, '-');
    }
}
