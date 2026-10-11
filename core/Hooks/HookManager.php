<?php

namespace Cms\Hooks;

final class HookManager
{
    /** @var array<string, array<int, array<int, array{callback: callable, args: int}>>> */
    private array $actions = [];

    /** @var array<string, array<int, array<int, array{callback: callable, args: int}>>> */
    private array $filters = [];

    /** @var array<string, int> */
    private array $doneActions = [];

    public function addAction(string $hook, callable $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
        $this->actions[$hook][$priority][] = ['callback' => $callback, 'args' => $acceptedArgs];
    }

    public function doAction(string $hook, mixed ...$args): void
    {
        $this->doneActions[$hook] = ($this->doneActions[$hook] ?? 0) + 1;

        if (empty($this->actions[$hook])) {
            return;
        }

        ksort($this->actions[$hook]);

        foreach ($this->actions[$hook] as $callbacks) {
            foreach ($callbacks as $entry) {
                call_user_func_array($entry['callback'], array_slice($args, 0, $entry['args']));
            }
        }
    }

    public function removeAction(string $hook, callable $callback, int $priority = 10): bool
    {
        return $this->remove($this->actions, $hook, $callback, $priority);
    }

    public function hasAction(string $hook): bool
    {
        return !empty($this->actions[$hook]);
    }

    public function didAction(string $hook): int
    {
        return $this->doneActions[$hook] ?? 0;
    }

    public function addFilter(string $hook, callable $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
        $this->filters[$hook][$priority][] = ['callback' => $callback, 'args' => $acceptedArgs];
    }

    public function applyFilters(string $hook, mixed $value, mixed ...$args): mixed
    {
        if (empty($this->filters[$hook])) {
            return $value;
        }

        ksort($this->filters[$hook]);

        foreach ($this->filters[$hook] as $callbacks) {
            foreach ($callbacks as $entry) {
                $callArgs = array_slice(array_merge([$value], $args), 0, $entry['args']);
                $value = call_user_func_array($entry['callback'], $callArgs);
            }
        }

        return $value;
    }

    public function removeFilter(string $hook, callable $callback, int $priority = 10): bool
    {
        return $this->remove($this->filters, $hook, $callback, $priority);
    }

    public function hasFilter(string $hook): bool
    {
        return !empty($this->filters[$hook]);
    }

    private function remove(array &$registry, string $hook, callable $callback, int $priority): bool
    {
        if (empty($registry[$hook][$priority])) {
            return false;
        }

        $removed = false;

        foreach ($registry[$hook][$priority] as $index => $entry) {
            if ($entry['callback'] === $callback) {
                unset($registry[$hook][$priority][$index]);
                $removed = true;
            }
        }

        return $removed;
    }
}
