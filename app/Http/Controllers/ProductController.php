<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Servicio de Cloudinary
     */
    protected $servicioCloudinary;

    /**
     * Constructor para aplicar middleware e inyectar servicios
     */
    public function __construct(\App\Services\ServicioCloudinary $servicioCloudinary)
    {
        $this->middleware('auth')->except(['index', 'show', 'search']);
        $this->middleware('admin')->except(['index', 'show', 'search']);
        $this->servicioCloudinary = $servicioCloudinary;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Product::with('category')->where('active', true);

        // Filtrar por categoría
        if ($request->has('categoria')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->categoria);
            });
        }

        // Filtrar por género
        if ($request->has('genero')) {
            $query->where('gender', $request->genero);
        }

        // Búsqueda por nombre y descripción
        if ($request->has('buscar') && $request->buscar) {
            $searchTerm = $request->buscar;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('category', function($categoryQuery) use ($searchTerm) {
                      $categoryQuery->where('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        // Ordenar productos
        $orderBy = $request->get('ordenar', 'name');
        $direction = $request->get('direccion', 'asc');
        $query->orderBy($orderBy, $direction);

        $products = $query->paginate(12);
        $categories = Category::where('active', true)->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Display a listing of the resource for admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function adminIndex()
    {
        $products = Product::with('category')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::where('active', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp,heic,heif|max:10240',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'size' => 'required|integer|min:1',
            'gender' => 'required|in:male,female,unisex',
            'active' => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            $urlImagen = $this->servicioCloudinary->subirImagen($request->file('image')->getRealPath());
            $validated['image'] = $urlImagen;
        }

        Product::create($validated);

        // Limpiar caché de la home
        Cache::forget('home_datos');

        return redirect()->route('admin.productos.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        if (!$product->active) {
            abort(404);
        }
        
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('active', true)
            ->with('category')
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $categories = Category::where('active', true)->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,heic,heif|max:10240',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'size' => 'required|integer|min:1',
            'gender' => 'required|in:male,female,unisex',
            'active' => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            // Eliminar imagen anterior de Cloudinary
            if ($product->image) {
                $this->servicioCloudinary->eliminarImagen($product->image);
            }

            $urlImagen = $this->servicioCloudinary->subirImagen($request->file('image')->getRealPath());
            $validated['image'] = $urlImagen;
        }

        $product->update($validated);

        // Limpiar caché de la home
        Cache::forget('home_datos');

        return redirect()->route('admin.productos.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            $this->servicioCloudinary->eliminarImagen($product->image);
        }

        $product->delete();

        // Limpiar caché de la home
        Cache::forget('home_datos');

        return redirect()->route('admin.productos.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }

    /**
     * API endpoint para búsqueda en tiempo real
     */
    public function search(Request $request)
    {
        $query = $request->get('query');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        try {
            $products = Product::where('active', true)
                ->where(function($q) use ($query) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($query) . '%'])
                      ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($query) . '%'])
                      ->orWhereHas('category', function($categoryQuery) use ($query) {
                          $categoryQuery->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($query) . '%']);
                      });
                })
                ->with('category')
                ->take(5)
                ->get()
                ->map(function($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'image' => $product->image_url,
                        'final_price' => (float) $product->price,
                        'url' => route('products.show', $product)
                    ];
                });
            
            return response()->json($products);
        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage());
            return response()->json([]);
        }
    }
}
