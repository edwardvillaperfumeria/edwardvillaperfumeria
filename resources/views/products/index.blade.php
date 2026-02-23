@extends('layouts.app')

@section('title', 'Catálogo de Perfumes - Edward Villa Perfumería')
@section('description', 'Descubre nuestra exclusiva colección de perfumes de lujo. Fragancias para hombre, mujer y unisex de las mejores marcas.')

@section('content')
<div class="row">
    <!-- Sidebar Filters (Desktop) -->
    <div class="col-lg-3 d-none d-lg-block">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 fw-bold">Filtros</h5>
            </div>
            <div class="card-body">
                <!-- Category Filter -->
                <div class="mb-4">
                    <h6 class="fw-semibold mb-3">Categorías</h6>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('products.index') }}" 
                           class="list-group-item list-group-item-action border-0 px-0 {{ !request('categoria') ? 'active' : '' }}">
                            Todas las categorías
                        </a>
                        @foreach($categories as $category)
                            <a href="{{ route('products.index', ['categoria' => $category->slug]) }}" 
                               class="list-group-item list-group-item-action border-0 px-0 {{ request('categoria') == $category->slug ? 'active' : '' }}">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Gender Filter -->
                <div class="mb-4">
                    <h6 class="fw-semibold mb-3">Género</h6>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('products.index', array_merge(request()->except('genero'), [])) }}" 
                           class="list-group-item list-group-item-action border-0 px-0 {{ !request('genero') ? 'active' : '' }}">
                            Todos
                        </a>
                        <a href="{{ route('products.index', array_merge(request()->except('genero'), ['genero' => 'male'])) }}" 
                           class="list-group-item list-group-item-action border-0 px-0 {{ request('genero') == 'male' ? 'active' : '' }}">
                            Masculino
                        </a>
                        <a href="{{ route('products.index', array_merge(request()->except('genero'), ['genero' => 'female'])) }}" 
                           class="list-group-item list-group-item-action border-0 px-0 {{ request('genero') == 'female' ? 'active' : '' }}">
                            Femenino
                        </a>
                        <a href="{{ route('products.index', array_merge(request()->except('genero'), ['genero' => 'unisex'])) }}" 
                           class="list-group-item list-group-item-action border-0 px-0 {{ request('genero') == 'unisex' ? 'active' : '' }}">
                            Unisex
                        </a>
                    </div>
                </div>

                <!-- Price Sort -->
                <div class="mb-4">
                    <h6 class="fw-semibold mb-3">Ordenar por precio</h6>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('products.index', array_merge(request()->all(), ['ordenar' => 'price', 'direccion' => 'asc'])) }}" 
                           class="list-group-item list-group-item-action border-0 px-0">
                            Menor a mayor
                        </a>
                        <a href="{{ route('products.index', array_merge(request()->all(), ['ordenar' => 'price', 'direccion' => 'desc'])) }}" 
                           class="list-group-item list-group-item-action border-0 px-0">
                            Mayor a menor
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="col-lg-9">
        <!-- Mobile Filters Button -->
        <div class="d-lg-none mb-3">
            <button class="btn btn-outline-dark w-100" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileFilters">
                <i class="fas fa-filter me-2"></i>Filtros
            </button>
        </div>

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">
                    @if(request('categoria'))
                        {{ $categories->where('slug', request('categoria'))->first()->name ?? 'Productos' }}
                    @elseif(request('genero'))
                        Perfumes {{ ucfirst(request('genero')) == 'Male' ? 'Masculinos' : (ucfirst(request('genero')) == 'Female' ? 'Femeninos' : 'Unisex') }}
                    @elseif(request('buscar'))
                        Resultados para "{{ request('buscar') }}"
                    @else
                        Nuestros Perfumes
                    @endif
                </h1>
                <p class="text-muted mb-0">{{ $products->total() }} productos encontrados</p>
            </div>
            
            <!-- Sort Dropdown (Mobile) -->
            <div class="dropdown d-lg-none">
                <button class="btn btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-sort me-1"></i>Ordenar
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('products.index', array_merge(request()->all(), ['ordenar' => 'name', 'direccion' => 'asc'])) }}">Nombre A-Z</a></li>
                    <li><a class="dropdown-item" href="{{ route('products.index', array_merge(request()->all(), ['ordenar' => 'price', 'direccion' => 'asc'])) }}">Precio: Menor a mayor</a></li>
                    <li><a class="dropdown-item" href="{{ route('products.index', array_merge(request()->all(), ['ordenar' => 'price', 'direccion' => 'desc'])) }}">Precio: Mayor a menor</a></li>
                </ul>
            </div>
        </div>

        <!-- Products Grid -->
        @if($products->count() > 0)
            <div class="row g-4">
                @foreach($products as $product)
                    <div class="col-6 col-md-4 col-xl-4">
                        <div class="product-card">
                            <!-- Product Image with Overlay -->
                            <div class="product-image-container">
                                <a href="{{ route('products.show', $product) }}" class="text-decoration-none">
                                    <img src="{{ $product->image_url }}"
                                         alt="{{ $product->name }}"
                                         class="product-image">
                                </a>
                                
                                <!-- Stock Status Badge -->
                                <div class="stock-badge">
                                    @if($product->stock > 0)
                                        <span class="badge badge-available">
                                            <i class="fas fa-check-circle me-1"></i>Disponible
                                        </span>
                                    @else
                                        <span class="badge badge-unavailable">
                                            <i class="fas fa-times-circle me-1"></i>Agotado
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Product Info -->
                            <div class="product-info">
                                <a href="{{ route('products.show', $product) }}" class="text-decoration-none">
                                    <!-- Category -->
                                    <div class="product-header">
                                        <span class="product-category">{{ $product->category->name }}</span>
                                    </div>
                                    
                                    <!-- Product Name -->
                                    <h3 class="product-title">{{ $product->name }}</h3>
                                    
                                    <!-- Price -->
                                    <div class="product-price-container">
                                        @if($product->hasActiveOffer())
                                            <div class="price-with-discount">
                                                <span class="current-price">${{ number_format($product->final_price, 2) }}</span>
                                                <small class="original-price text-decoration-line-through text-muted">
                                                    ${{ number_format($product->price, 2) }}
                                                </small>
                                            </div>
                                        @else
                                            <div class="price-normal">
                                                <span class="current-price">${{ number_format($product->price, 2) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Size and Gender -->
                                    <div class="product-meta">
                                        <span class="product-size">{{ $product->size }}ml</span>
                                        <span class="badge badge-gender">
                                            {{ $product->gender == 'male' ? 'Masculino' : ($product->gender == 'female' ? 'Femenino' : 'Unisex') }}
                                        </span>
                                    </div>
                                </a>
                                
                                <!-- Add to Cart Button -->
                                @if($product->stock > 0)
                                    <form action="{{ route('cart.add') }}" method="POST" class="mt-2">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-add-cart w-100">
                                            <i class="fas fa-shopping-cart me-2"></i>Agregar
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-out-of-stock w-100 mt-2" disabled>
                                        <i class="fas fa-ban me-2"></i>Agotado
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-5">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @else
            <!-- No Products Found -->
            <div class="text-center py-5">
                <i class="fas fa-search text-muted mb-3" style="font-size: 4rem;"></i>
                <h3 class="text-muted">No se encontraron productos</h3>
                <p class="text-muted">Intenta ajustar tus filtros o buscar algo diferente.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary-custom">
                    Ver todos los productos
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Mobile Filters Offcanvas -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileFilters">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Filtros</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <!-- Category Filter -->
        <div class="mb-4">
            <h6 class="fw-semibold mb-3">Categorías</h6>
            <div class="list-group list-group-flush">
                <a href="{{ route('products.index') }}" 
                   class="list-group-item list-group-item-action border-0 px-0 {{ !request('categoria') ? 'active' : '' }}"
                   data-bs-dismiss="offcanvas">
                    Todas las categorías
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('products.index', ['categoria' => $category->slug]) }}" 
                       class="list-group-item list-group-item-action border-0 px-0 {{ request('categoria') == $category->slug ? 'active' : '' }}"
                       data-bs-dismiss="offcanvas">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Gender Filter -->
        <div class="mb-4">
            <h6 class="fw-semibold mb-3">Género</h6>
            <div class="list-group list-group-flush">
                <a href="{{ route('products.index', array_merge(request()->except('genero'), [])) }}" 
                   class="list-group-item list-group-item-action border-0 px-0 {{ !request('genero') ? 'active' : '' }}"
                   data-bs-dismiss="offcanvas">
                    Todos
                </a>
                <a href="{{ route('products.index', array_merge(request()->except('genero'), ['genero' => 'male'])) }}" 
                   class="list-group-item list-group-item-action border-0 px-0 {{ request('genero') == 'male' ? 'active' : '' }}"
                   data-bs-dismiss="offcanvas">
                    Masculino
                </a>
                <a href="{{ route('products.index', array_merge(request()->except('genero'), ['genero' => 'female'])) }}" 
                   class="list-group-item list-group-item-action border-0 px-0 {{ request('genero') == 'female' ? 'active' : '' }}"
                   data-bs-dismiss="offcanvas">
                    Femenino
                </a>
                <a href="{{ route('products.index', array_merge(request()->except('genero'), ['genero' => 'unisex'])) }}" 
                   class="list-group-item list-group-item-action border-0 px-0 {{ request('genero') == 'unisex' ? 'active' : '' }}"
                   data-bs-dismiss="offcanvas">
                    Unisex
                </a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .list-group-item.active {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }
    
    .list-group-item:hover {
        background-color: var(--light-gray);
    }

    /* Product Card Styles */
    .product-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        border: 1px solid rgba(0,0,0,0.05);
    }

    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }

    /* Product Image Container */
    .product-image-container {
        position: relative;
        overflow: hidden;
    }

    .product-image {
        width: 100%;
        height: 320px;
        object-fit: cover;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        transition: transform 0.3s ease;
    }

    .product-card:hover .product-image {
        transform: scale(1.05);
    }

    /* Stock Badge */
    .stock-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        z-index: 2;
    }

    .badge-available {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        font-size: 0.75rem;
        padding: 0.5rem 0.8rem;
        border-radius: 20px;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }

    .badge-unavailable {
        background: linear-gradient(135deg, #dc3545, #e74c3c);
        color: white;
        font-size: 0.75rem;
        padding: 0.5rem 0.8rem;
        border-radius: 20px;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
    }

    /* Product Info */
    .product-info {
        padding: 1.2rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    /* Product Header */
    .product-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .product-category {
        color: var(--medium-gray);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Product Title */
    .product-title {
        font-size: 1.1rem;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
        line-height: 1.2;
        font-weight: 600;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Product Price Container - Fixed height to prevent layout shift */
    .product-price-container {
        height: 60px;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        margin-bottom: 0.5rem;
    }
    
    .price-with-discount {
        display: flex;
        flex-direction: column;
    }
    
    .price-normal {
        display: flex;
        align-items: flex-start;
    }
    
    .current-price {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--secondary-color);
        line-height: 1.2;
    }
    
    .original-price {
        font-size: 0.9rem;
        margin-top: 0.2rem;
        line-height: 1;
    }
    
    .discount-badge {
        background: var(--gold);
        color: white;
        padding: 0.2rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-top: 0.25rem;
        align-self: flex-start;
    }

    /* Product Meta */
    .product-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .product-size {
        color: var(--medium-gray);
        font-size: 0.85rem;
    }

    .badge-gender {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
        border-radius: 8px;
        background: var(--primary-color);
        color: white;
    }

    /* Buttons */
    .btn-add-cart {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border: none;
        padding: 1rem 1.5rem;
        border-radius: 15px;
        font-weight: 700;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        color: white;
        text-transform: uppercase;
        font-size: 0.85rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .btn-add-cart:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
        color: white;
    }

    .btn-out-of-stock {
        background: #6c757d;
        border: none;
        padding: 1rem 1.5rem;
        border-radius: 15px;
        font-weight: 700;
        letter-spacing: 0.5px;
        color: white;
        text-transform: uppercase;
        font-size: 0.85rem;
        opacity: 0.7;
    }

    .btn-primary-custom {
        background: var(--primary-color);
        border: none;
        padding: 1rem;
        border-radius: 10px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }

    .btn-primary-custom:hover {
        background: var(--secondary-color);
        transform: translateY(-2px);
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .product-image {
            height: 280px;
        }

        .product-info {
            padding: 1.5rem;
        }

        .product-title {
            font-size: 1.2rem;
        }
        
        .price-main {
            font-size: 1.4rem;
        }

        .btn-add-cart, .btn-out-of-stock {
            padding: 0.9rem 1.2rem;
            font-size: 0.8rem;
        }
    }

    @media (max-width: 576px) {
        .product-image {
            height: 250px;
        }

        .product-info {
            padding: 1.2rem;
        }

        .product-title {
            font-size: 1.1rem;
        }
        
        .price-main {
            font-size: 1.3rem;
        }

        .product-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .badge-gender {
            align-self: flex-end;
        }

        .btn-add-cart, .btn-out-of-stock {
            padding: 0.8rem 1rem;
            font-size: 0.75rem;
        }
    }
</style>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cartForms = document.querySelectorAll('form[action*="cart/add"]');
        cartForms.forEach(form => {
            form.addEventListener('submit', function() {
                const btn = form.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Agregando...';
                }
            });
        });
    });
</script>
@endpush
@endsection
