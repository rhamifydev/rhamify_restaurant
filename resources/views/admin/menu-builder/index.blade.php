@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Menu Builder</h1>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h4>Create Menu</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.menu-builder.menus.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Menu Name</label>
                            <input type="text" name="name" class="form-control" placeholder="main_menu" required>
                        </div>
                        <button class="btn btn-primary" type="submit">Create Menu</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>Menus</h4>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse ($menus as $menu)
                            <a class="list-group-item list-group-item-action {{ optional($selectedMenu)->id === $menu->id ? 'active' : '' }}"
                                href="{{ route('admin.menu-builder.index', ['menu' => $menu->id]) }}">
                                {{ $menu->name }}
                            </a>
                        @empty
                            <p class="mb-0 text-muted">No menus created yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            @if ($selectedMenu)
                <div class="card card-primary">
                    <div class="card-header">
                        <h4>Edit {{ $selectedMenu->name }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.menu-builder.menus.update', $selectedMenu->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Menu Name</label>
                                        <input type="text" name="name" class="form-control" value="{{ $selectedMenu->name }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button class="btn btn-primary mr-2" type="submit">Save Name</button>
                                    <button class="btn btn-danger" type="submit" form="delete-menu-form"
                                        onclick="return confirm('Delete this menu and all its items?')">Delete</button>
                                </div>
                            </div>
                        </form>
                        <form id="delete-menu-form" action="{{ route('admin.menu-builder.menus.destroy', $selectedMenu->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4>Add Menu Item</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.menu-builder.items.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="menu_id" value="{{ $selectedMenu->id }}">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Label</label>
                                        <input type="text" name="label" class="form-control" placeholder="Home" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Link</label>
                                        <input type="text" name="link" class="form-control" placeholder="/" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Parent</label>
                                        <select name="parent_id" class="form-control">
                                            <option value="0">No Parent</option>
                                            @foreach ($items as $item)
                                                <option value="{{ $item->id }}">{{ str_repeat('- ', (int) $item->depth) }}{{ $item->label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Sort</label>
                                        <input type="number" name="sort" class="form-control" value="{{ $items->count() + 1 }}" min="0">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>CSS Class</label>
                                        <input type="text" name="class" class="form-control" placeholder="optional">
                                    </div>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button class="btn btn-primary btn-block" type="submit">Add Item</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4>Menu Items</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th style="min-width: 160px;">Label</th>
                                        <th style="min-width: 180px;">Link</th>
                                        <th style="min-width: 140px;">Parent</th>
                                        <th style="width: 95px;">Sort</th>
                                        <th style="min-width: 130px;">Class</th>
                                        <th style="width: 150px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($items as $item)
                                        <tr>
                                            <form action="{{ route('admin.menu-builder.items.update', $item->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="menu_id" value="{{ $selectedMenu->id }}">
                                                <td>
                                                    <input type="text" name="label" class="form-control"
                                                        value="{{ $item->label }}" required>
                                                </td>
                                                <td>
                                                    <input type="text" name="link" class="form-control"
                                                        value="{{ $item->link }}" required>
                                                </td>
                                                <td>
                                                    <select name="parent_id" class="form-control">
                                                        <option value="0">No Parent</option>
                                                        @foreach ($items as $parent)
                                                            @if ($parent->id !== $item->id)
                                                                <option value="{{ $parent->id }}" @selected($item->parent_id == $parent->id)>
                                                                    {{ str_repeat('- ', (int) $parent->depth) }}{{ $parent->label }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="sort" class="form-control"
                                                        value="{{ $item->sort }}" min="0">
                                                </td>
                                                <td>
                                                    <input type="text" name="class" class="form-control"
                                                        value="{{ $item->class }}">
                                                </td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm" type="submit">Save</button>
                                                    <button class="btn btn-danger btn-sm" type="submit"
                                                        form="delete-menu-item-{{ $item->id }}"
                                                        onclick="return confirm('Delete this menu item?')">Delete</button>
                                                </td>
                                            </form>
                                            <form id="delete-menu-item-{{ $item->id }}"
                                                action="{{ route('admin.menu-builder.items.destroy', $item->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No menu items yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center text-muted">
                        Create a menu to start adding links.
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
