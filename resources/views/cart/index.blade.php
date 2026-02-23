@extends('layouts.app')

@section('title', 'Carrito de Compras - Edward Villa Perfumería')
@section('description', 'Revisa los productos en tu carrito de compras y procede al checkout en Edward Villa Perfumería.')

@section('content')
<div class="cart-container">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="cart-title">Mi Carrito</h1>
            <p class="text-muted mb-0">
                @if($cartItems->count() > 0)
                    {{ $cartItems->count() }} {{ $cartItems->count() == 1 ? 'producto' : 'productos' }} en tu carrito
                @else
                    Tu carrito está vacío
                @endif
            </p>
        </div>
    </div>

    @if($cartItems->count() > 0)
        <div class="row">
            <!-- Cart Items -->
            <div class="col-12">
                <div class="cart-items">
                    @foreach($cartItems as $item)
                        <div class="cart-item" id="cart-item-{{ $item->id }}">
                            <div class="row align-items-center">
                                <!-- Product Image -->
                                <div class="col-3 col-md-2">
                                    <div class="cart-item-image">
                                        <img src="{{ $item->product->image_url }}"
                                             alt="{{ $item->product->name }}"
                                             class="img-fluid rounded">
                                    </div>
                                </div>

                                <!-- Product Info -->
                                <div class="col-6 col-md-5">
                                    <div class="cart-item-info">
                                        <h5 class="cart-item-name">
                                            <a href="{{ route('products.show', $item->product) }}" 
                                               class="text-decoration-none">
                                                {{ $item->product->name }}
                                            </a>
                                        </h5>
                                        <p class="cart-item-category">{{ $item->product->category->name }}</p>
                                        <p class="cart-item-size">{{ $item->product->size }}ml</p>
                                    </div>
                                </div>

                                <!-- Quantity Controls -->
                                <div class="col-3 col-md-2">
                                    <div class="quantity-controls">
                                        <form action="{{ route('cart.update', $item->id) }}" method="POST" class="quantity-form">
                                            @csrf
                                            @method('PUT')
                                            <div class="input-group input-group-sm">
                                                <button type="button" class="btn btn-outline-secondary quantity-btn" 
                                                        data-action="decrease" data-item-id="{{ $item->id }}" data-quantity="{{ $item->quantity }}">
                                                    @if($item->quantity == 1)
                                                        <i class="fas fa-trash"></i>
                                                    @else
                                                        <i class="fas fa-minus"></i>
                                                    @endif
                                                </button>
                                                <input type="number" name="quantity" 
                                                       class="form-control text-center quantity-input" 
                                                       value="{{ $item->quantity }}" 
                                                       min="1" max="{{ $item->product->stock }}">
                                                <button type="button" class="btn btn-outline-secondary quantity-btn" 
                                                        data-action="increase" data-item-id="{{ $item->id }}" data-quantity="{{ $item->quantity }}">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </form>
                                        
                                        <!-- Hidden form for removal -->
                                        <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="remove-form" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </div>

                                <!-- Price -->
                                <div class="col-12 col-md-3">
                                    <div class="cart-item-total">
                                        @if($item->product->hasActiveOffer())
                                            <div class="d-flex flex-column align-items-end">
                                                <span class="fw-bold">${{ number_format($item->product->final_price * $item->quantity, 2) }}</span>
                                                <small class="text-muted text-decoration-line-through">
                                                    ${{ number_format($item->product->price * $item->quantity, 2) }}
                                                </small>
                                                <span class="discount-badge">-{{ $item->product->discount_percentage }}%</span>
                                            </div>
                                        @else
                                            <span class="fw-bold">${{ number_format($item->product->price * $item->quantity, 2) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Checkout Actions -->
                <div class="checkout-actions mt-4">
                    <div class="d-flex justify-content-between flex-wrap gap-3">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-dark btn-lg flex-fill">
                            <i class="fas fa-arrow-left me-2"></i>Continuar Comprando
                        </a>
                        
                        <a href="{{ route('checkout.shipping') }}" class="btn btn-primary-custom btn-lg flex-fill">
                            <i class="fas fa-arrow-right me-2"></i>Siguiente
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty Cart -->
        <div class="empty-cart text-center py-5">
            <div class="empty-cart-icon mb-4">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <h3 class="mb-3">Tu carrito está vacío</h3>
            <p class="text-muted mb-4">¡Descubre nuestras increíbles fragancias y encuentra tu perfume perfecto!</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary-custom btn-lg">
                <i class="fas fa-search me-2"></i>Explorar Productos
            </a>
        </div>
    @endif
</div>

@push('styles')
<style>
    .cart-title {
        font-family: var(--font-primary);
        font-size: 2rem;
        font-weight: 600;
        color: var(--primary-color);
    }
    
    .cart-item {
        background: white;
        border: none;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }
    
    .cart-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(0,0,0,0.12);
    }
    
    /* Imagen más grande y redondeada */
    .cart-item-image {
        width: 100%;
        height: 100px;
        position: relative;
        overflow: hidden;
    }
    
    .cart-item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .cart-item-image:hover img {
        transform: scale(1.05);
    }
    
    .placeholder-image {
        width: 100%;
        height: 100px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    /* Tipografía normal como estaba antes */
    .cart-item-name {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        line-height: 1.3;
        color: var(--primary-color);
    }
    
    .cart-item-name a {
        color: inherit;
        transition: color 0.3s ease;
    }
    
    .cart-item-name a:hover {
        color: var(--secondary-color);
    }
    
    /* Categoría y volumen separados */
    .cart-item-category {
        color: var(--medium-gray);
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }
    
    .cart-item-size {
        color: var(--medium-gray);
        font-size: 0.85rem;
        margin-bottom: 0;
    }
    
    /* Botones de cantidad más pequeños y ajustados */
    .quantity-controls {
        max-width: 100px;
        margin-left: -10px;
    }
    
    .quantity-input {
        border-left: none;
        border-right: none;
        font-size: 0.9rem;
        padding: 0.25rem 0.5rem;
        width: 40px;
        flex: 0 0 40px;
    }
    
    .quantity-btn {
        border: 1px solid #ced4da;
        width: 30px;
        height: 30px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        flex: 0 0 30px;
    }
    
    .input-group {
        display: flex;
        align-items: center;
        width: 100px;
    }
    
    /* Precio sin gradiente */
    .cart-item-total {
        text-align: right;
    }
    
    .cart-item-total .fw-bold {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--primary-color);
    }
    
    .empty-cart-icon {
        font-size: 5rem;
        color: var(--medium-gray);
    }
    
    .empty-cart h3 {
        color: var(--primary-color);
    }
    
    /* Discount Badge */
    .discount-badge {
        background: var(--gold);
        color: white;
        padding: 0.2rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-top: 0.25rem;
    }
    
    @media (max-width: 768px) {
        .cart-title {
            font-size: 2rem;
        }
        
        .cart-item {
            padding: 1.5rem;
        }
        
        .cart-item-image {
            height: 80px;
        }
        
        .placeholder-image {
            height: 80px;
        }
        
        .cart-item-name {
            font-size: 1.2rem;
        }
        
        .quantity-controls {
            max-width: 120px;
        }
        
        .quantity-btn {
            width: 35px;
            height: 35px;
        }
        
        .cart-item-meta {
            font-size: 0.9rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function updateCartAjax(itemId, quantity) {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('_method', 'PUT');
        formData.append('quantity', quantity);

        fetch(`{{ url('/carrito/actualizar') }}/${itemId}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(html => {
            // Reemplazar solo el contenido del carrito
            const parser = new DOMParser();
            const newDoc = parser.parseFromString(html, 'text/html');
            const newCartContainer = newDoc.querySelector('.cart-container');
            
            if (newCartContainer) {
                document.querySelector('.cart-container').innerHTML = newCartContainer.innerHTML;
                // Reinicializar los event listeners
                initializeCartEvents();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Si hay error, recargar la página
            location.reload();
        });
    }

    function removeCartAjax(itemId) {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('_method', 'DELETE');

        fetch(`{{ url('/carrito/eliminar') }}/${itemId}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(html => {
            // Reemplazar solo el contenido del carrito
            const parser = new DOMParser();
            const newDoc = parser.parseFromString(html, 'text/html');
            const newCartContainer = newDoc.querySelector('.cart-container');
            
            if (newCartContainer) {
                document.querySelector('.cart-container').innerHTML = newCartContainer.innerHTML;
                // Reinicializar los event listeners
                initializeCartEvents();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Si hay error, recargar la página
            location.reload();
        });
    }

    function initializeCartEvents() {
        // Quantity controls con AJAX
        document.querySelectorAll('.quantity-btn').forEach(button => {
            button.addEventListener('click', function() {
                const action = this.dataset.action;
                const itemId = this.dataset.itemId;
                const input = this.closest('.quantity-form').querySelector('.quantity-input');
                const currentValue = parseInt(input.value);
                const max = parseInt(input.getAttribute('max'));
                const currentQuantity = parseInt(this.dataset.quantity);
                
                if (action === 'increase' && currentValue < max) {
                    updateCartAjax(itemId, currentValue + 1);
                } else if (action === 'decrease') {
                    if (currentQuantity === 1) {
                        // Si la cantidad es 1, eliminar el producto
                        removeCartAjax(itemId);
                    } else {
                        // Si la cantidad es mayor a 1, decrementar
                        updateCartAjax(itemId, currentValue - 1);
                    }
                }
            });
        });
        
        // Auto-submit on quantity change con AJAX
        document.querySelectorAll('.quantity-input').forEach(input => {
            let timeout;
            input.addEventListener('input', function() {
                const itemId = this.closest('.quantity-form').querySelector('.quantity-btn').dataset.itemId;
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    const value = parseInt(this.value);
                    const max = parseInt(this.getAttribute('max'));
                    if (value >= 1 && value <= max) {
                        updateCartAjax(itemId, value);
                    }
                }, 500);
            });
        });
    }

    // Inicializar eventos cuando se carga la página
    document.addEventListener('DOMContentLoaded', function() {
        initializeCartEvents();
    });
</script>
@endpush
@endsection
