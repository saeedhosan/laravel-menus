<?php

declare(strict_types=1);

namespace SaeedHosan\Menus;

class MenuFilter
{
    protected ?string $whereKey = null;

    protected mixed $whereValue = null;

    protected ?string $position = null;

    protected string $positionKey = 'slug';

    protected mixed $positionValue = null;

    protected bool $asSubmenu = false;

    /**
     * @var callable|null
     */
    protected $filterCallback = null;

    /**
     * The menu name for the filter
     */
    public function __construct(protected string|MenuBuilder $name) {}

    public function getName(): string|MenuBuilder
    {
        return $this->name;
    }

    /**
     * callback every item
     */
    public function filter(callable $callback): self
    {
        $this->filterCallback = $callback;

        return $this;
    }

    public function where(string $key, mixed $value = null): self
    {
        $this->whereKey = $key;
        $this->whereValue = $value;

        return $this;
    }

    public function whereSlug(mixed $value): self
    {
        return $this->where('slug', $value);
    }

    public function after(string $value, string $key = 'slug'): self
    {
        $this->position = 'after';
        $this->positionKey = $key;
        $this->positionValue = $value;

        return $this;
    }

    public function before(string $value, string $key = 'slug'): self
    {
        $this->position = 'before';
        $this->positionKey = $key;
        $this->positionValue = $value;

        return $this;
    }

    // fore to be submenu
    public function asSubmenu(): self
    {
        $this->asSubmenu = true;

        return $this;
    }

    /**
     * Apply the filter rules.
     *
     * @param  list<array<mixed>>  $items
     * @param  list<array<mixed>>  $incoming
     * @return list<array<mixed>>
     */
    public function apply(array $items, array $incoming = []): array
    {
        $filtered = [];

        if ($this->filterCallback) {
            foreach ($incoming as $item) {
                if (call_user_func($this->filterCallback, $item)) {
                    $filtered[] = $item;
                }
            }
        } else {
            $filtered = $incoming;
        }

        if ($filtered === []) {
            return $items;
        }

        if ($this->whereKey !== null || $this->asSubmenu) {
            return $this->insertIntoSubmenu($items, $filtered);
        }

        if ($this->position !== null) {
            return $this->insertByPosition($items, $filtered);
        }

        return array_merge($items, $filtered);
    }

    /**
     * @return list<array<mixed>>
     */
    private function normalizeItems(mixed $items): array
    {
        if (! is_array($items)) {
            return [];
        }

        $normalized = [];

        foreach ($items as $item) {
            if (is_array($item)) {
                $normalized[] = $item;
            }
        }

        return $normalized;
    }

    /**
     * @param  list<array<mixed>>  $items
     * @param  list<array<mixed>>  $incoming
     * @return list<array<mixed>>
     */
    private function insertIntoSubmenu(array $items, array $incoming): array
    {
        $key = $this->whereKey ?? $this->positionKey;
        $value = $this->whereValue ?? $this->positionValue;

        if ($value === null) {
            return array_merge($items, $incoming);
        }

        $result = $this->insertIntoSubmenuInternal(
            $items,
            $incoming,
            $key,
            $value
        );

        $inserted = ($result['inserted'] ?? false) === true;
        $resultItems = $this->normalizeItems($result['items'] ?? null);

        if ($inserted) {
            return $resultItems;
        }

        return array_merge($items, $incoming);
    }

    /**
     * @param  list<array<mixed>>  $items
     * @param  list<array<mixed>>  $incoming
     * @return array<string, mixed>
     */
    private function insertIntoSubmenuInternal(
        array $items,
        array $incoming,
        string $key,
        mixed $value
    ): array {
        $resultItems = [];
        $inserted = false;

        foreach ($items as $item) {
            $itemValue = data_get($item, $key);
            $submenu = data_get($item, 'submenu');
            $submenuItems = $this->normalizeItems($submenu);

            if ($itemValue === $value) {
                $submenu = array_merge($submenuItems, $incoming);

                $item['submenu'] = $submenu;
                $inserted = true;
            } elseif ($submenuItems !== []) {
                $childResult = $this->insertIntoSubmenuInternal(
                    $submenuItems,
                    $incoming,
                    $key,
                    $value
                );

                if (($childResult['inserted'] ?? false) === true) {
                    $inserted = true;
                }

                $item['submenu'] = $this->normalizeItems($childResult['items'] ?? null);
            }

            $resultItems[] = $item;
        }

        return [
            'items' => $resultItems,
            'inserted' => $inserted,
        ];
    }

    /**
     * @param  list<array<mixed>>  $items
     * @param  list<array<mixed>>  $incoming
     * @return list<array<mixed>>
     */
    private function insertByPosition(array $items, array $incoming): array
    {
        if ($this->position === null || $this->positionValue === null) {
            return array_merge($items, $incoming);
        }

        $result = $this->insertByPositionInternal($items, $incoming);

        $inserted = ($result['inserted'] ?? false) === true;
        $resultItems = $this->normalizeItems($result['items'] ?? null);

        if ($inserted) {
            return $resultItems;
        }

        return array_merge($items, $incoming);
    }

    /**
     * @param  list<array<mixed>>  $items
     * @param  list<array<mixed>>  $incoming
     * @return array<string, mixed>
     */
    private function insertByPositionInternal(array $items, array $incoming): array
    {
        $resultItems = [];
        $inserted = false;

        foreach ($items as $item) {

            $itemValue = data_get($item, $this->positionKey);
            $submenu = data_get($item, 'submenu');
            $matches = $itemValue === $this->positionValue;

            if (! $matches) {

                $submenuItems = $this->normalizeItems($submenu);

                if ($submenuItems !== []) {

                    $childResult = $this->insertByPositionInternal($submenuItems, $incoming);

                    if (($childResult['inserted'] ?? false) === true) {
                        $inserted = true;
                    }

                    $item['submenu'] = $this->normalizeItems($childResult['items'] ?? null);
                }

                $resultItems[] = $item;

                continue;
            }

            $inserted = true;
            $chunk = $this->position === 'before'
                ? array_merge($incoming, [$item])
                : array_merge([$item], $incoming);

            foreach ($chunk as $entry) {
                $resultItems[] = $entry;
            }
        }

        return [
            'items' => $resultItems,
            'inserted' => $inserted,
        ];
    }
}
