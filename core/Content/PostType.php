<?php

namespace Cms\Content;

class PostType
{
    public function __construct(
        public readonly string $slug,
        public readonly string $label,
        public readonly string $labelSingular,
        public readonly bool $hasArchive = false,
        public readonly array $supports = ['title', 'editor'],
        public readonly string $menuIcon = 'file-text',
        public readonly bool $showInAdminMenu = true,
    ) {
    }
}
