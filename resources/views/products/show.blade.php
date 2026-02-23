@extends('layouts.app')

@section('title', $product->name . ' - Edward Villa Perfumería')
@section('description', 'Compra ' . $product->name . ' - ' . Str::limit($product->description, 150) . ' Envío gratis en Edward Villa Perfumería.')

@section('content')
<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb breadcrumb-custom">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Productos</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index', ['categoria' => $product->category->slug]) }}">{{ $product->category->name }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
    </ol>
</nav>

<div class="row g-4 g-lg-5">
    <!-- Imagen del Producto -->
    <div class="col-lg-6 mb-3">
        <div class="detalle-imagen-contenedor">
            <img src="{{ $product->image_url }}"
                 alt="{{ $product->name }}"
                 class="detalle-imagen">

            <!-- Badge de Stock sobre la imagen -->
            <div class="detalle-stock-badge">
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
    </div>

    <!-- Detalles del Producto -->
    <div class="col-lg-6">
        <div class="detalle-info">
            <!-- Categoria y Genero -->
            <div class="detalle-badges mb-3">
                <span class="detalle-categoria">{{ $product->category->name }}</span>
                <span class="badge badge-gender">
                    {{ $product->gender == 'male' ? 'Masculino' : ($product->gender == 'female' ? 'Femenino' : 'Unisex') }}
                </span>
            </div>

            <!-- Nombre del Producto -->
            <h1 class="detalle-titulo">{{ $product->name }}</h1>

            <!-- Precio y Tamano -->
            <div class="detalle-precio-seccion">
                <div class="detalle-precio-info">
                    @if($product->hasActiveOffer())
                        <div class="detalle-precio-oferta">
                            <span class="detalle-precio-actual">${{ number_format($product->final_price, 2) }}</span>
                            <span class="detalle-descuento-badge">-{{ $product->discount_percentage }}%</span>
                        </div>
                        <span class="detalle-precio-original">${{ number_format($product->price, 2) }}</span>
                    @else
                        <span class="detalle-precio-actual">${{ number_format($product->price, 2) }}</span>
                    @endif
                </div>
                <span class="detalle-tamano">
                    <i class="fas fa-flask me-1"></i>{{ $product->size }}ml
                </span>
            </div>

            <!-- Agregar al Carrito -->
            @if($product->stock > 0)
                <div class="detalle-carrito-seccion">
                    <form action="{{ route('cart.add') }}" method="POST" class="detalle-carrito-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        
                        <div class="row g-3 align-items-end">
                            <div class="col-4">
                                <label for="quantity" class="detalle-label">Cantidad</label>
                                <select name="quantity" id="quantity" class="detalle-select">
                                    @for($i = 1; $i <= min(10, $product->stock); $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-8">
                                <button type="submit" class="btn btn-add-cart w-100">
                                    <i class="fas fa-shopping-cart me-2"></i>Agregar al Carrito
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <div class="detalle-stock-info mt-3">
                        <span>{{ $product->stock }} unidades disponibles</span>
                    </div>

                    @guest
                        <div class="detalle-sesion-aviso mt-3">
                            <i class="fas fa-info-circle me-1"></i>
                            Para finalizar tu compra necesitaras <a href="{{ route('login') }}">iniciar sesion</a>
                        </div>
                    @endguest
                </div>
            @else
                <div class="detalle-carrito-seccion">
                    <button class="btn btn-out-of-stock w-100" disabled>
                        <i class="fas fa-ban me-2"></i>Producto Agotado
                    </button>
                </div>
            @endif

            <!-- Descripcion corta -->
            @if($product->description)
                <div class="detalle-descripcion-card mt-4">
                    <h6 class="detalle-seccion-titulo">
                        <i class="fas fa-align-left me-2"></i>Descripcion
                    </h6>
                    <p class="detalle-descripcion-texto">{{ $product->description }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Especificaciones e Informacion -->
<div class="row g-4 mt-4">
    <div class="col-md-6">
        <div class="detalle-card-info">
            <div class="detalle-specs-lista">
                <div class="detalle-spec-item">
                    <div class="detalle-spec-icono">
                        <i class="fas fa-flask"></i>
                    </div>
                    <div class="detalle-spec-contenido">
                        <span class="detalle-spec-label">Tamano</span>
                        <span class="detalle-spec-valor">{{ $product->size }}ml</span>
                    </div>
                </div>
                <div class="detalle-spec-item">
                    <div class="detalle-spec-icono">
                        <i class="fas fa-venus-mars"></i>
                    </div>
                    <div class="detalle-spec-contenido">
                        <span class="detalle-spec-label">Genero</span>
                        <span class="detalle-spec-valor">{{ $product->gender == 'male' ? 'Masculino' : ($product->gender == 'female' ? 'Femenino' : 'Unisex') }}</span>
                    </div>
                </div>
                <div class="detalle-spec-item">
                    <div class="detalle-spec-icono">
                        <i class="fas fa-tag"></i>
                    </div>
                    <div class="detalle-spec-contenido">
                        <span class="detalle-spec-label">Categoria</span>
                        <span class="detalle-spec-valor">{{ $product->category->name }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Productos Relacionados -->
@if($relatedProducts->count() > 0)
<div class="detalle-relacionados mt-5">
    <div class="text-center mb-4">
        <h2 class="section-title">Productos Relacionados</h2>
        <p class="section-subtitle">Te podrian interesar estas fragancias</p>
    </div>
    <div class="row g-4">
        @foreach($relatedProducts as $relatedProduct)
            <div class="col-6 col-md-3">
                <div class="product-card">
                    <!-- Imagen del Producto con Overlay -->
                    <div class="product-image-container">
                        <a href="{{ route('products.show', $relatedProduct) }}" class="text-decoration-none">
                            <img src="{{ $relatedProduct->image_url }}"
                                 alt="{{ $relatedProduct->name }}"
                                 class="product-image">
                        </a>
                        
                        <!-- Badge de Stock -->
                        <div class="stock-badge">
                            @if($relatedProduct->stock > 0)
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
                    
                    <!-- Info del Producto -->
                    <div class="product-info">
                        <a href="{{ route('products.show', $relatedProduct) }}" class="text-decoration-none">
                            <!-- Categoria -->
                            <div class="product-header">
                                <span class="product-category">{{ $relatedProduct->category->name }}</span>
                            </div>
                            
                            <!-- Nombre -->
                            <h3 class="product-title">{{ $relatedProduct->name }}</h3>
                            
                            <!-- Precio -->
                            <div class="product-price-container">
                                @if($relatedProduct->hasActiveOffer())
                                    <div class="price-with-discount">
                                        <span class="current-price">${{ number_format($relatedProduct->final_price, 2) }}</span>
                                        <small class="original-price text-decoration-line-through text-muted">
                                            ${{ number_format($relatedProduct->price, 2) }}
                                        </small>
                                    </div>
                                @else
                                    <div class="price-normal">
                                        <span class="current-price">${{ number_format($relatedProduct->price, 2) }}</span>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Tamano y Genero -->
                            <div class="product-meta">
                                <span class="product-size">{{ $relatedProduct->size }}ml</span>
                                <span class="badge badge-gender">
                                    {{ $relatedProduct->gender == 'male' ? 'Masculino' : ($relatedProduct->gender == 'female' ? 'Femenino' : 'Unisex') }}
                                </span>
                            </div>
                        </a>
                        
                        <!-- Boton Agregar al Carrito -->
                        @if($relatedProduct->stock > 0)
                            <form action="{{ route('cart.add') }}" method="POST" class="mt-2">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $relatedProduct->id }}">
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
</div>
@endif

@push('styles')
<style>
    /* ================================================
       PAGINA DE DETALLES DEL PRODUCTO
       Estilos consistentes con el catalogo e inicio
       ================================================ */

    /* Breadcrumb */
    .breadcrumb-custom {
        background: none;
        padding: 0;
        margin-bottom: 0;
    }

    .breadcrumb-custom .breadcrumb-item a {
        color: var(--medium-gray);
        text-decoration: none;
        font-size: 0.9rem;
        transition: color 0.2s ease;
    }

    .breadcrumb-custom .breadcrumb-item a:hover {
        color: var(--primary-color);
    }

    .breadcrumb-custom .breadcrumb-item.active {
        color: var(--primary-color);
        font-weight: 500;
        font-size: 0.9rem;
    }

    /* Contenedor de Imagen */
    .detalle-imagen-contenedor {
        position: relative;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 400px;
        max-height: 520px;
        padding: 1.5rem;
    }

    .detalle-imagen {
        max-width: 100%;
        max-height: 480px;
        width: auto;
        height: auto;
        object-fit: contain;
        display: block;
        margin: 0 auto;
        transition: transform 0.4s ease;
    }

    .detalle-imagen-contenedor:hover .detalle-imagen {
        transform: scale(1.03);
    }

    .detalle-stock-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        z-index: 2;
    }

    /* Badges de Disponibilidad (reutilizados del catalogo) */
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

    /* Info del Producto */
    .detalle-info {
        padding: 0.5rem 0;
    }

    .detalle-badges {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .detalle-categoria {
        color: var(--medium-gray);
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        font-weight: 500;
    }

    .badge-gender {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
        border-radius: 8px;
        background: var(--primary-color);
        color: white;
    }

    /* Titulo del Producto */
    .detalle-titulo {
        font-family: var(--font-display);
        font-size: 2.2rem;
        font-weight: 600;
        color: var(--primary-color);
        line-height: 1.2;
        margin-bottom: 1.5rem;
    }

    /* Precio */
    .detalle-precio-seccion {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem;
        background: linear-gradient(135deg, #f8f9fa, #fff);
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.06);
        margin-bottom: 1.5rem;
    }

    .detalle-precio-info {
        display: flex;
        flex-direction: column;
    }

    .detalle-precio-oferta {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .detalle-precio-actual {
        font-size: 2.2rem;
        font-weight: 700;
        color: var(--primary-color);
        line-height: 1;
    }

    .detalle-descuento-badge {
        background: var(--gold);
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 15px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .detalle-precio-original {
        font-size: 1.1rem;
        color: var(--medium-gray);
        text-decoration: line-through;
        margin-top: 0.3rem;
    }

    .detalle-tamano {
        font-size: 1.1rem;
        color: var(--medium-gray);
        font-weight: 500;
        background: white;
        padding: 0.5rem 1rem;
        border-radius: 10px;
        border: 1px solid rgba(0,0,0,0.08);
    }

    /* Seccion Agregar al Carrito */
    .detalle-carrito-seccion {
        margin-bottom: 1rem;
    }

    .detalle-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
        display: block;
    }

    .detalle-select {
        width: 100%;
        padding: 0.85rem 1rem;
        border: 1px solid rgba(0,0,0,0.1);
        border-radius: 15px;
        font-size: 1rem;
        background: white;
        appearance: auto;
        cursor: pointer;
        transition: border-color 0.2s ease;
    }

    .detalle-select:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(0,0,0,0.05);
    }

    /* Boton Agregar al Carrito (igual al catalogo) */
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

    .detalle-stock-info {
        font-size: 0.85rem;
        color: var(--medium-gray);
        display: flex;
        align-items: center;
    }

    .detalle-sesion-aviso {
        font-size: 0.85rem;
        color: var(--medium-gray);
        background: var(--light-gray);
        padding: 0.75rem 1rem;
        border-radius: 10px;
    }

    .detalle-sesion-aviso a {
        color: var(--primary-color);
        font-weight: 600;
        text-decoration: none;
    }

    .detalle-sesion-aviso a:hover {
        text-decoration: underline;
    }

    /* Tarjeta de Descripcion */
    .detalle-descripcion-card {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        border: 1px solid rgba(0,0,0,0.06);
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    .detalle-seccion-titulo {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 0.75rem;
    }

    .detalle-descripcion-texto {
        font-size: 0.95rem;
        line-height: 1.7;
        color: var(--dark-gray);
        margin-bottom: 0;
    }

    /* Tarjetas de Informacion */
    .detalle-card-info {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        height: 100%;
    }

    .detalle-card-titulo {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--light-gray);
    }

    /* Especificaciones */
    .detalle-specs-lista {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .detalle-spec-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem;
        background: var(--light-gray);
        border-radius: 12px;
        transition: transform 0.2s ease;
    }

    .detalle-spec-item:hover {
        transform: translateX(5px);
    }

    .detalle-spec-icono {
        width: 40px;
        height: 40px;
        min-width: 40px;
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gold);
        font-size: 0.9rem;
    }

    .detalle-spec-contenido {
        display: flex;
        flex-direction: column;
    }

    .detalle-spec-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--medium-gray);
        font-weight: 500;
    }

    .detalle-spec-valor {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--primary-color);
    }

    /* Informacion de Envio */
    .detalle-envio-lista {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
    }

    .detalle-envio-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.9rem;
        color: var(--dark-gray);
    }

    .detalle-envio-check {
        color: #28a745;
        font-size: 1rem;
    }

    /* Features Compactas (reutilizadas del home) */
    .feature-item-mini {
        padding: 0.75rem 1rem;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        height: 100%;
        border: 1px solid rgba(0,0,0,0.05);
    }

    .feature-icon-mini {
        width: 44px;
        height: 44px;
        min-width: 44px;
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gold);
        font-size: 1.1rem;
    }

    .feature-item-mini h6 {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--primary-color);
    }

    .feature-item-mini small {
        color: var(--medium-gray);
        font-size: 0.75rem;
    }

    /* Seccion Productos Relacionados */
    .section-title {
        font-family: var(--font-display);
        font-size: 2rem;
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
    }

    .section-subtitle {
        font-size: 1rem;
        color: var(--medium-gray);
        margin-bottom: 1rem;
    }

    /* Tarjetas de Producto Relacionado (mismo estilo del catalogo) */
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

    .product-image-container {
        position: relative;
        overflow: hidden;
    }

    .product-image {
        width: 100%;
        height: 220px;
        object-fit: cover;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        transition: transform 0.3s ease;
    }

    .product-card:hover .product-image {
        transform: scale(1.05);
    }

    .stock-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        z-index: 2;
    }

    .product-info {
        padding: 1.2rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

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

    .product-title {
        font-size: 1rem;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
        line-height: 1.2;
        font-weight: 600;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-price-container {
        height: 50px;
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
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--secondary-color);
        line-height: 1.2;
    }

    .original-price {
        font-size: 0.85rem;
        margin-top: 0.2rem;
        line-height: 1;
    }

    .product-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .product-size {
        color: var(--medium-gray);
        font-size: 0.85rem;
    }

    /* ================================================
       RESPONSIVE DESIGN
       ================================================ */
    @media (max-width: 991px) {
        .detalle-titulo {
            font-size: 1.8rem;
        }

        .detalle-precio-actual {
            font-size: 1.8rem;
        }

        .detalle-imagen-contenedor {
            min-height: 300px;
            max-height: 400px;
        }
    }

    @media (max-width: 768px) {
        .detalle-titulo {
            font-size: 1.5rem;
        }

        .detalle-precio-actual {
            font-size: 1.6rem;
        }

        .detalle-imagen-contenedor {
            min-height: 250px;
            max-height: 340px;
            padding: 1rem;
        }

        .detalle-imagen {
            max-height: 300px;
        }

        .detalle-precio-seccion {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .section-title {
            font-size: 1.6rem;
        }

        .product-image {
            height: 180px;
        }
    }

    @media (max-width: 576px) {
        .detalle-titulo {
            font-size: 1.3rem;
        }

        .detalle-precio-actual {
            font-size: 1.5rem;
        }

        .detalle-imagen-contenedor {
            min-height: 200px;
            max-height: 280px;
            border-radius: 15px;
        }

        .detalle-imagen {
            max-height: 240px;
        }

        .detalle-card-info {
            border-radius: 15px;
            padding: 1.25rem;
        }

        .section-title {
            font-size: 1.4rem;
        }

        .product-image {
            height: 150px;
        }

        .btn-add-cart, .btn-out-of-stock {
            padding: 0.85rem 1rem;
            font-size: 0.8rem;
        }
    }
</style>
@endpush

@endsection
