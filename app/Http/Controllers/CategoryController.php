<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories for admin.
     */
    public function index()
    {
        $categories = Category::withCount('products')->latest()->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string|max:500'
        ]);
        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description
        ]);

        // Limpiar caché de la home
        Cache::forget('home_datos');

        return redirect()->route('admin.categorias.index')
                        ->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Display the specified category (public view).
     */
    public function show(Category $categoria)
    {
        $productos = Product::where('category_id', $categoria->id)
                          ->where('stock', '>', 0)
                          ->latest()
                          ->paginate(12);

        return view('categories.show', compact('categoria', 'productos'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $categoria)
    {
        return view('admin.categories.edit', compact('categoria'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $categoria)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $categoria->id,
            'description' => 'nullable|string|max:500'
        ]);
        $categoria->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description
        ]);

        // Limpiar caché de la home
        Cache::forget('home_datos');

        return redirect()->route('admin.categorias.index')
                        ->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $categoria)
    {
        // Check if category has products
        if ($categoria->products()->count() > 0) {
            return redirect()->route('admin.categorias.index')
                            ->with('error', 'No se puede eliminar la categoría porque tiene productos asociados.');
        }

        $categoria->delete();

        // Limpiar caché de la home
        Cache::forget('home_datos');

        return redirect()->route('admin.categorias.index')
                        ->with('success', 'Categoría eliminada exitosamente.');
    }
}
