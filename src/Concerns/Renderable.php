<?php

declare(strict_types=1);

namespace SaeedHosan\Menus\Concerns;

trait Renderable
{
    /**
     * @return list<array<mixed>>
     */
    public function toArray(): array
    {
        return $this->items();
    }

    public function toHtml(): string
    {
        return view('laravel-menus::rootmenu', ['items' => $this->toArray()])->render();
    }

    public function render(): self
    {
        $this->build();

        return $this;
    }
}
