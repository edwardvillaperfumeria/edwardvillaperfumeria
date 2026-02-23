@extends('layouts.app')

@section('title', 'Pedido #' . $order->id . ' - Edward Villa Perfumería')
@section('description', 'Detalles del pedido #' . $order->id . ' en Edward Villa Perfumería.')

@section('content')
<div class="order-details-container">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="order-title">Pedido #{{ $order->id }}</h1>
            <p class="text-muted mb-0">Realizado el {{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div>
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver a Mis Pedidos
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Order Summary -->
        <div class="col-lg-8">
            <div class="order-summary-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-bag me-2"></i>Resumen del Pedido
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Order Status -->
                    <div class="order-status-section mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6>Estado del Pedido:</h6>
                            <span class="badge badge-status badge-{{ $order->status }}">
                                @switch($order->status)
                                    @case('pending')
                                        <i class="fas fa-clock me-1"></i>Pendiente
                                        @break
                                    @case('processing')
                                        <i class="fas fa-cog me-1"></i>Procesando
                                        @break
                                    @case('shipped')
                                        <i class="fas fa-truck me-1"></i>Enviado
                                        @break
                                    @case('delivered')
                                        <i class="fas fa-check-circle me-1"></i>Entregado
                                        @break
                                    @case('paid')
                                        <i class="fas fa-check-double me-1"></i>Pagado
                                        @break
                                    @case('cancelled')
                                        <i class="fas fa-times-circle me-1"></i>Cancelado
                                        @break
                                    @default
                                        {{ ucfirst($order->status) }}
                                @endswitch
                            </span>
                        </div>
                        
                        <!-- Status Progress -->
                        <div class="status-progress mt-3">
                            <div class="progress-steps">
                                <div class="step {{ in_array($order->status, ['pending', 'paid', 'processing', 'shipped', 'delivered']) ? 'active' : '' }}">
                                    <div class="step-icon">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                    <span>{{ $order->status === 'paid' ? 'Pagado' : 'Pedido Realizado' }}</span>
                                </div>
                                <div class="step {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'active' : '' }}">
                                    <div class="step-icon">
                                        <i class="fas fa-cog"></i>
                                    </div>
                                    <span>Procesando</span>
                                </div>
                                <div class="step {{ in_array($order->status, ['shipped', 'delivered']) ? 'active' : '' }}">
                                    <div class="step-icon">
                                        <i class="fas fa-truck"></i>
                                    </div>
                                    <span>Enviado</span>
                                </div>
                                <div class="step {{ $order->status === 'delivered' ? 'active' : '' }}">
                                    <div class="step-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <span>Entregado</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="order-items-section">
                        <h6 class="mb-3">Productos Pedidos:</h6>
                        @foreach($order->items as $item)
                            <div class="order-item-detail">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        @if($item->product->image_url)
                                            <img src="{{ $item->product->image_url }}" 
                                                 alt="{{ $item->product->name }}" 
                                                 class="order-item-image">
                                        @else
                                            <div class="order-item-image d-flex align-items-center justify-content-center bg-light">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="item-name">{{ $item->product->name }}</h6>
                                        <p class="item-category">{{ $item->product->category->name }}</p>
                                        <p class="item-size">{{ $item->product->size }}ml</p>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <span class="item-quantity">{{ $item->quantity }}</span>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <div class="item-price">${{ number_format($item->price, 2) }}</div>
                                        <div class="item-total">
                                            <strong>${{ number_format($item->price * $item->quantity, 2) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Info Sidebar -->
        <div class="col-lg-4">
            <!-- Order Total -->
            <div class="order-total-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-calculator me-2"></i>Total del Pedido
                    </h5>
                </div>
                <div class="card-body">
                    <div class="total-breakdown">
                        <div class="total-row">
                            <span>Subtotal:</span>
                            <span>${{ number_format($order->total, 2) }}</span>
                        </div>
                        <div class="total-row">
                            <span>Envío:</span>
                            <span class="text-success">Gratis</span>
                        </div>
                        <hr>
                        <div class="total-row total-final">
                            <span><strong>Total:</strong></span>
                            <span><strong>${{ number_format($order->total, 2) }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            @if($order->shipping_address)
                <div class="shipping-info-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i>Información de Envío
                        </h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Dirección:</strong></p>
                        <p>{{ $order->shipping_address }}</p>
                        
                        @if($order->phone)
                            <p><strong>Teléfono:</strong></p>
                            <p>{{ $order->phone }}</p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Order Notes -->
            @if($order->notes)
                <div class="order-notes-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-sticky-note me-2"></i>Notas del Pedido
                        </h5>
                    </div>
                    <div class="card-body">
                        <p>{{ $order->notes }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .order-title {
        font-family: var(--font-display);
        font-size: 2.5rem;
        font-weight: 600;
        color: var(--primary-color);
    }
    
    .order-summary-card, .order-total-card, .shipping-info-card, .order-notes-card {
        background: white;
        border: none;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    
    .card-header {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-bottom: 1px solid #dee2e6;
        padding: 1.5rem;
    }
    
    .card-header h5 {
        color: var(--primary-color);
        font-weight: 600;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .badge-status {
        font-size: 0.9rem;
        padding: 0.6rem 1.2rem;
        border-radius: 25px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .badge-pending {
        background: linear-gradient(135deg, #ffc107, #ffb300);
        color: white;
    }
    
    .badge-processing {
        background: linear-gradient(135deg, #17a2b8, #138496);
        color: white;
    }
    
    .badge-shipped {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
    }
    
    .badge-delivered {
        background: linear-gradient(135deg, #28a745, #1e7e34);
        color: white;
    }
    
    .badge-cancelled {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
    }
    
    .status-progress {
        margin-top: 2rem;
    }
    
    .progress-steps {
        display: flex;
        justify-content: space-between;
        position: relative;
    }
    
    .progress-steps::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 2px;
        background: #e9ecef;
        z-index: 1;
    }
    
    .step {
        text-align: center;
        position: relative;
        z-index: 2;
        flex: 1;
    }
    
    .step-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e9ecef;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.5rem;
        transition: all 0.3s ease;
    }
    
    .step.active .step-icon {
        background: var(--primary-color);
        color: white;
    }
    
    .step span {
        font-size: 0.8rem;
        color: #6c757d;
        font-weight: 500;
    }
    
    .step.active span {
        color: var(--primary-color);
        font-weight: 600;
    }
    
    .order-item-detail {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        border: 1px solid #e9ecef;
    }
    
    .order-item-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 10px;
    }
    
    .item-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 0.25rem;
    }
    
    .item-category {
        color: var(--medium-gray);
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }
    
    .item-size {
        color: var(--medium-gray);
        font-size: 0.85rem;
        margin-bottom: 0;
    }
    
    .item-quantity {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--primary-color);
        background: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    
    .item-price {
        font-size: 1rem;
        color: var(--medium-gray);
    }
    
    .item-total {
        font-size: 1.2rem;
        color: var(--primary-color);
        font-weight: 600;
    }
    
    .total-breakdown {
        font-size: 1.1rem;
    }
    
    .total-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.75rem;
    }
    
    .total-final {
        font-size: 1.3rem;
        color: var(--primary-color);
    }
    
    @media (max-width: 768px) {
        .order-title {
            font-size: 2rem;
        }
        
        .progress-steps {
            flex-direction: column;
            gap: 1rem;
        }
        
        .progress-steps::before {
            display: none;
        }
        
        .step {
            display: flex;
            align-items: center;
            text-align: left;
        }
        
        .step-icon {
            margin-right: 1rem;
            margin-bottom: 0;
        }
        
        .order-item-detail .row {
            text-align: center;
        }
        
        .order-item-detail .col-md-6 {
            margin: 1rem 0;
        }
    }
</style>
@endpush
@endsection
