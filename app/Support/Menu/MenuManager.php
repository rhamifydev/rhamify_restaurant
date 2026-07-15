<?php

namespace App\Support\Menu;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\HtmlString;

class MenuManager
{
    public function getByName(string $name): array
    {
        if (! Schema::hasTable('admin_menus') || ! Schema::hasTable('admin_menu_items')) {
            return [];
        }

        $menu = DB::table('admin_menus')->where('name', $name)->first();

        if (! $menu) {
            return [];
        }

        $items = DB::table('admin_menu_items')
            ->where('menu_id', $menu->id)
            ->orderBy('sort')
            ->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'label' => $item->label,
                'link' => $item->link,
                'parent_id' => $item->parent_id,
                'sort' => $item->sort,
                'class' => $item->class,
                'child' => [],
            ])
            ->keyBy('id')
            ->toArray();

        $tree = [];

        foreach ($items as $id => &$item) {
            if ($item['parent_id'] && isset($items[$item['parent_id']])) {
                $items[$item['parent_id']]['child'][] = &$item;
                continue;
            }

            $tree[$id] = &$item;
        }

        return array_values($tree);
    }

    public function render(): HtmlString
    {
        return new HtmlString(view('admin.menu-builder.compat')->render());
    }

    public function scripts(): HtmlString
    {
        return new HtmlString('');
    }

    public function select(string $name, array $items = []): HtmlString
    {
        $options = collect($items)
            ->map(fn ($label, $value) => '<option value="'.e($value).'">'.e($label).'</option>')
            ->implode('');

        return new HtmlString('<select name="'.e($name).'" class="form-control">'.$options.'</select>');
    }
}
