<?php

namespace Cms\Settings;

use Cms\Database\Connection;

class OptionsRepository
{
    private array $cache = [];
    private bool $loaded = false;

    public function __construct(private Connection $connection)
    {
    }

    private function loadAutoloaded(): void
    {
        if ($this->loaded) {
            return;
        }

        foreach ($this->connection->select('options', ['option_name', 'option_value'], ['autoload' => 1]) as $row) {
            $this->cache[$row['option_name']] = $this->decode($row['option_value']);
        }

        $this->loaded = true;
    }

    public function get(string $name, mixed $default = null): mixed
    {
        $this->loadAutoloaded();

        if (array_key_exists($name, $this->cache)) {
            return $this->cache[$name];
        }

        $value = $this->connection->get('options', 'option_value', ['option_name' => $name]);

        if ($value === null) {
            return $default;
        }

        return $this->cache[$name] = $this->decode($value);
    }

    public function set(string $name, mixed $value, bool $autoload = true): void
    {
        $encoded = $this->encode($value);
        $exists = $this->connection->get('options', 'id', ['option_name' => $name]);

        if ($exists) {
            $this->connection->update('options', ['option_value' => $encoded, 'autoload' => (int) $autoload], ['option_name' => $name]);
        } else {
            $this->connection->insert('options', [
                'option_name' => $name,
                'option_value' => $encoded,
                'autoload' => (int) $autoload,
            ]);
        }

        $this->cache[$name] = $value;
    }

    private function encode(mixed $value): string
    {
        return is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : (string) $value;
    }

    private function decode(?string $value): mixed
    {
        if ($value === null) {
            return null;
        }

        $decoded = json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE && (is_array($decoded)) ? $decoded : $value;
    }
}
