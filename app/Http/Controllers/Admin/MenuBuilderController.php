<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MenuBuilderController extends Controller
{
    function index() : View {
        $menus = DB::table('admin_menus')->orderBy('name')->get();
        $selectedMenuId = request('menu', $menus->first()->id ?? null);
        $selectedMenu = $selectedMenuId
            ? DB::table('admin_menus')->where('id', $selectedMenuId)->first()
            : null;
        $items = $selectedMenu
            ? DB::table('admin_menu_items')->where('menu_id', $selectedMenu->id)->orderBy('sort')->orderBy('id')->get()
            : collect();

        return view('admin.menu-builder.index', compact('menus', 'selectedMenu', 'items'));
    }

    function storeMenu(Request $request) : RedirectResponse {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:admin_menus,name'],
        ]);

        $menuId = DB::table('admin_menus')->insertGetId([
            'name' => $data['name'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        toastr()->success('Menu created successfully!');

        return to_route('admin.menu-builder.index', ['menu' => $menuId]);
    }

    function updateMenu(Request $request, string $menu) : RedirectResponse {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('admin_menus', 'name')->ignore($menu)],
        ]);

        DB::table('admin_menus')->where('id', $menu)->update([
            'name' => $data['name'],
            'updated_at' => now(),
        ]);

        toastr()->success('Menu updated successfully!');

        return to_route('admin.menu-builder.index', ['menu' => $menu]);
    }

    function destroyMenu(string $menu) : RedirectResponse {
        DB::table('admin_menus')->where('id', $menu)->delete();

        toastr()->success('Menu deleted successfully!');

        return to_route('admin.menu-builder.index');
    }

    function storeItem(Request $request) : RedirectResponse {
        $data = $this->validateItem($request);
        $data['created_at'] = now();
        $data['updated_at'] = now();

        DB::table('admin_menu_items')->insert($data);

        toastr()->success('Menu item added successfully!');

        return to_route('admin.menu-builder.index', ['menu' => $data['menu_id']]);
    }

    function updateItem(Request $request, string $item) : RedirectResponse {
        $data = $this->validateItem($request, $item);
        $data['updated_at'] = now();

        DB::table('admin_menu_items')->where('id', $item)->update($data);

        toastr()->success('Menu item updated successfully!');

        return to_route('admin.menu-builder.index', ['menu' => $data['menu_id']]);
    }

    function destroyItem(string $item) : RedirectResponse {
        $menuId = DB::table('admin_menu_items')->where('id', $item)->value('menu_id');

        DB::table('admin_menu_items')->where('parent_id', $item)->update([
            'parent_id' => 0,
            'depth' => 0,
            'updated_at' => now(),
        ]);
        DB::table('admin_menu_items')->where('id', $item)->delete();

        toastr()->success('Menu item deleted successfully!');

        return to_route('admin.menu-builder.index', ['menu' => $menuId]);
    }

    private function validateItem(Request $request, ?string $itemId = null): array
    {
        $data = $request->validate([
            'menu_id' => ['required', 'integer', 'exists:admin_menus,id'],
            'label' => ['required', 'string', 'max:255'],
            'link' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'class' => ['nullable', 'string', 'max:255'],
        ]);

        $parentId = (int) ($data['parent_id'] ?? 0);

        if ($itemId && $parentId === (int) $itemId) {
            $parentId = 0;
        }

        $parent = $parentId > 0
            ? DB::table('admin_menu_items')->where('menu_id', $data['menu_id'])->where('id', $parentId)->first()
            : null;

        $data['parent_id'] = $parent ? $parent->id : 0;
        $data['depth'] = $parent ? ((int) $parent->depth + 1) : 0;
        $data['sort'] = (int) ($data['sort'] ?? 0);
        $data['class'] = $data['class'] ?? null;

        return $data;
    }
}
