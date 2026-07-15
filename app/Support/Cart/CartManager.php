<?php

namespace App\Support\Cart;

use Illuminate\Support\Collection;

class CartManager
{
    protected string $sessionKey = 'shopping_cart';

    public function add(array $item): CartItem
    {
        $cartItem = new CartItem($item);
        $content = $this->rawContent();
        $content[$cartItem->rowId] = $cartItem->toArray();
        session()->put($this->sessionKey, $content);

        return $cartItem;
    }

    public function content(): Collection
    {
        return collect($this->rawContent())->map(fn (array $item) => new CartItem($item));
    }

    public function count(): int
    {
        return $this->content()->count();
    }

    public function get(string $rowId): ?CartItem
    {
        $item = $this->rawContent()[$rowId] ?? null;

        return $item ? new CartItem($item) : null;
    }

    public function update(string $rowId, int $qty): ?CartItem
    {
        $content = $this->rawContent();

        if (! isset($content[$rowId])) {
            return null;
        }

        $content[$rowId]['qty'] = $qty;
        session()->put($this->sessionKey, $content);

        return new CartItem($content[$rowId]);
    }

    public function remove(string $rowId): void
    {
        $content = $this->rawContent();
        unset($content[$rowId]);
        session()->put($this->sessionKey, $content);
    }

    public function destroy(): void
    {
        session()->forget($this->sessionKey);
    }

    protected function rawContent(): array
    {
        return session()->get($this->sessionKey, []);
    }
}
