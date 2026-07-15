<?php

namespace App\Support\Cart;

use Illuminate\Support\Fluent;
use Illuminate\Support\Str;

class CartItem
{
    public string $rowId;

    public int|string $id;

    public string $name;

    public int $qty;

    public float|int $price;

    public float|int $weight;

    public Fluent $options;

    public function __construct(array $attributes)
    {
        $this->id = $attributes['id'];
        $this->name = $attributes['name'];
        $this->qty = (int) $attributes['qty'];
        $this->price = $attributes['price'];
        $this->weight = $attributes['weight'] ?? 0;
        $this->options = new Fluent($attributes['options'] ?? []);
        $this->rowId = $attributes['rowId'] ?? $this->makeRowId();
    }

    public function subtotal(): float|int
    {
        return $this->price * $this->qty;
    }

    public function toArray(): array
    {
        return [
            'rowId' => $this->rowId,
            'id' => $this->id,
            'name' => $this->name,
            'qty' => $this->qty,
            'price' => $this->price,
            'weight' => $this->weight,
            'options' => $this->options->toArray(),
        ];
    }

    protected function makeRowId(): string
    {
        return md5($this->id.serialize($this->options->toArray()).Str::random(8));
    }
}
