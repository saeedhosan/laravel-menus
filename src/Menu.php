<?php

declare(strict_types=1);

namespace SaeedHosan\Menus;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use SaeedHosan\Menus\Concerns\Makeable;
use SaeedHosan\Menus\Concerns\MenuAccessableCallback;
use SaeedHosan\Menus\Concerns\Renderable;

/**
 * @implements Arrayable<int, array<string, mixed>>
 */
class Menu implements Arrayable, Htmlable
{
    use Makeable,MenuAccessableCallback, Renderable;

    /**
     * @var array<string, array<string, mixed>>
     */
    private static array $templates = [];

    /**
     * @var list<array<mixed>>|null
     */
    private ?array $items = null;

    /**
     * @var Collection<string, array<string, mixed>>
     */
    private Collection $buildTemplates;

    /**
     * @var array<string, list<array<mixed>>>
     */
    private array $buildCache = [];

    /**
     * @var array<string, bool>
     */
    private array $buildResolving = [];

    /**
     * @var array<string, list<string>>
     */
    private array $buildChildren = [];

    public function __construct()
    {
        $this->buildTemplates = collect();
    }

    public static function flush(): void
    {
        self::$templates = [];
    }

    public static function create(string|MenuBuilder $builder, ?string $parent = null): MenuFilter
    {
        $filter = new MenuFilter($builder);

        $builderClass = self::resolveBuilderClass($builder);

        self::$templates[$builderClass]['builder'] = $builder;
        self::$templates[$builderClass]['parent'] = $parent;
        self::$templates[$builderClass]['filter'] = $filter;

        return $filter;
    }

    public static function update(string|MenuBuilder $builder, ?string $parent = null): MenuFilter
    {
        $filter = new MenuFilter($builder);

        $builderClass = self::resolveBuilderClass($builder);

        self::$templates[$builderClass]['builder'] = $builder;
        self::$templates[$builderClass]['parent'] = $parent;
        self::$templates[$builderClass]['filter'] = $filter;

        return $filter;
    }

    /**
     * @return list<array<mixed>>
     */
    public function items(): array
    {
        if ($this->items === null) {
            $this->build();
        }

        /** @var list<array<mixed>> $items */
        $items = $this->items ?? [];

        return $items;
    }

    private static function resolveBuilderClass(string|MenuBuilder $builder): string
    {
        return $builder instanceof MenuBuilder ? $builder::class : $builder;
    }

    private function build(): void
    {

        if (self::$templates === []) {

            $this->items = [];

            return;
        }

        $buildTemplates = collect(self::$templates)->map(function (array $template, string $key): array {

            /** @var array<string, mixed> $template */
            $rawBuilder = $template['builder'] ?? $key;

            if (! is_string($rawBuilder) && ! ($rawBuilder instanceof MenuBuilder)) {
                $rawBuilder = $key;
            }

            $builderClass = self::resolveBuilderClass($rawBuilder);

            $parent = $template['parent'] ?? null;

            if (! is_string($parent)) {
                $parent = null;
            }

            $filter = $template['filter'] ?? new MenuFilter($builderClass);

            if (! $filter instanceof MenuFilter) {
                $filter = new MenuFilter($builderClass);
            }

            return [
                'builder' => $builderClass,
                'instance' => $this->resolveBuilderInstance($rawBuilder),
                'parent' => $parent,
                'filter' => $filter,
            ];

        })->keyBy('builder');

        /** @var Collection<string, array<string, mixed>> $buildTemplates */
        $this->buildTemplates = $buildTemplates;

        $this->buildCache = [];
        $this->buildResolving = [];
        $this->buildChildren = [];

        foreach ($this->buildTemplates as $builderClass => $template) {
            $parent = $template['parent'] ?? null;

            if (! is_string($parent)) {
                continue;
            }

            $this->buildChildren[$parent][] = $builderClass;
        }

        $rootBuilders = [];

        foreach ($this->buildTemplates as $builderClass => $template) {
            if ($template['parent'] === null) {
                $rootBuilders[] = $builderClass;
            }
        }

        $items = [];

        foreach ($rootBuilders as $builderClass) {
            foreach ($this->buildMenuItems($builderClass) as $item) {
                $items[] = $item;
            }
        }

        $this->items = $items;

        $this->buildTemplates = collect();
        $this->buildCache = [];
        $this->buildResolving = [];
        $this->buildChildren = [];
    }

    private function resolveBuilderInstance(string|MenuBuilder $builder): MenuBuilder
    {
        if ($builder instanceof MenuBuilder) {
            return $builder;
        }

        /** @var MenuBuilder $instance */
        $instance = app($builder);

        return $instance;
    }

    /**
     * @param  list<array<mixed>>|MenuBuilder|null  $items
     * @return list<array<mixed>>
     */
    private function resolveItems(MenuBuilder|array|null $items): array
    {
        if ($items instanceof MenuBuilder) {
            return $items->getItems();
        }

        /** @var list<array<mixed>> $wrapped */
        $wrapped = array_values(Arr::wrap($items));

        return $wrapped;
    }

    /**
     * @param  list<array<mixed>>  $items
     * @return list<array<mixed>>
     */
    private function filterAccessible(array $items): array
    {
        $filtered = [];

        foreach ($items as $item) {
            if (array_key_exists('access', $item) && self::$accessFilterCallback !== null) {
                if (! call_user_func(self::$accessFilterCallback, $item['access'])) {
                    continue;
                }
            }

            if (isset($item['submenu'])) {
                /** @var list<array<mixed>> $submenu */
                $submenu = array_values(Arr::wrap($item['submenu']));
                $item['submenu'] = $this->filterAccessible($submenu);
            }

            $filtered[] = $item;
        }

        return $filtered;
    }

    /**
     * @return list<array<mixed>>
     */
    private function buildMenuItems(string $builderClass): array
    {
        if (array_key_exists($builderClass, $this->buildCache)) {
            return $this->buildCache[$builderClass];
        }

        if (isset($this->buildResolving[$builderClass])) {
            return [];
        }

        $this->buildResolving[$builderClass] = true;

        $template = $this->buildTemplates->get($builderClass);

        if (! $template) {
            $this->buildCache[$builderClass] = [];
            unset($this->buildResolving[$builderClass]);

            return [];
        }

        $instance = $template['instance'] ?? null;
        if (! $instance instanceof MenuBuilder) {
            $this->buildCache[$builderClass] = [];
            unset($this->buildResolving[$builderClass]);

            return [];
        }

        $items = $this->resolveItems($instance);

        foreach ($this->buildChildren[$builderClass] ?? [] as $childBuilderClass) {
            $child = $this->buildTemplates->get($childBuilderClass);

            if (! $child) {
                continue;
            }

            $childItems = $this->buildMenuItems($childBuilderClass);
            $filter = $child['filter'] ?? null;
            if (! $filter instanceof MenuFilter) {
                continue;
            }

            $items = $filter->apply($items, $childItems);
        }

        $items = $this->filterAccessible($items);

        $this->buildCache[$builderClass] = $items;
        unset($this->buildResolving[$builderClass]);

        return $items;
    }
}
