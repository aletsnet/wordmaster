<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('items')->get();
        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        return view('admin.menus.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'slug' => 'nullable|unique:menus,slug',
            'location' => 'nullable|max:255',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        Menu::create($data);

        return redirect()->route('admin.menus.index')
            ->with('success', __('messages.menu_created'));
    }

    public function edit(Menu $menu)
    {
        $menu->load('items.children');
        $pages = Post::ofType('page')->published()->get();
        $categories = Category::all();
        return view('admin.menus.edit', compact('menu', 'pages', 'categories'));
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'slug' => 'nullable|unique:menus,slug,' . $menu->id,
            'location' => 'nullable|max:255',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $menu->update($data);

        return redirect()->route('admin.menus.index')
            ->with('success', __('messages.menu_updated'));
    }

    public function addItem(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'title' => 'required|max:255',
            'url' => 'nullable|max:255',
            'type' => 'required|in:custom,page,category',
            'target_id' => 'nullable|integer',
            'parent_id' => 'nullable|exists:menu_items,id',
        ]);

        if ($data['type'] === 'page' && isset($data['target_id'])) {
            $page = Post::find($data['target_id']);
            $data['url'] = '/' . ($page ? $page->slug : '');
        } elseif ($data['type'] === 'category' && isset($data['target_id'])) {
            $cat = Category::find($data['target_id']);
            $data['url'] = '/category/' . ($cat ? $cat->slug : '');
        }

        $maxOrder = MenuItem::where('menu_id', $menu->id)->max('order');
        $data['order'] = ($maxOrder ?? 0) + 1;
        $data['menu_id'] = $menu->id;

        MenuItem::create($data);

        return redirect()->route('admin.menus.edit', $menu)
            ->with('success', __('messages.menu_item_added'));
    }

    public function updateItem(Request $request, Menu $menu, MenuItem $item)
    {
        $data = $request->validate([
            'title' => 'required|max:255',
            'url' => 'nullable|max:255',
            'order' => 'nullable|integer',
            'parent_id' => 'nullable|exists:menu_items,id',
        ]);

        $item->update($data);

        return redirect()->route('admin.menus.edit', $menu)
            ->with('success', __('messages.menu_item_updated'));
    }

    public function removeItem(Menu $menu, MenuItem $item)
    {
        $item->children()->update(['parent_id' => null]);
        $item->delete();
        return redirect()->route('admin.menus.edit', $menu)
            ->with('success', __('messages.menu_item_deleted'));
    }

    public function destroy(Menu $menu)
    {
        $menu->items()->delete();
        $menu->delete();
        return redirect()->route('admin.menus.index')
            ->with('success', __('messages.menu_deleted'));
    }
}
