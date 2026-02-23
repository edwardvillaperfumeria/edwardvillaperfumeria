@extends('layouts.app')

@section('title', 'Mis Pedidos - Edward Villa Perfumería')
@section('description', 'Revisa el estado de tus pedidos en Edward Villa Perfumería.')

@section('content')
<div class="orders-container">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="orders-title">Mis Pedidos</h1>
            <p class="text-muted mb-0">
                @if($orders->count() > 0)
                    {{ $orders->total() }} {{ $orders->total() == 1 ? 'pedido encontrado' : 'pedidos encontrados' }}
                @else
                    No tienes pedidos realizados
                @endif
            </p>
        </div>
    </div>

    @if($orders->count() > 0)
        <div class="orders-list">
            @foreach($orders as $order)
                <div class="order-card">
                    <div class="order-header">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="order-number">Pedido #{{ $order->id }}</h5>
                                <p class="order-date">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <span class="badge badge-status badge-{{ $order->status }}">
                                    @switch($order->status)
                                        @case('pending')
                                            Pendiente
                                            @break
                                        @case('processing')
                                            Procesando
                                            @break
                                        @case('shipped')
                                            Enviado
                                            @break
                                        @case('delivered')
                                            Entregado
                                            @break
                                        @case('paid')
                                            Pagado
                                            @break
                                        @case('cancelled')
                                            Cancelado
                                            @break
                                        @default
                                            {{ ucfirst($order->status) }}
                                    @endswitch
                                </span>
                                <div class="order-total mt-2">
                                    <strong>${{ number_format($order->total, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="order-items">
                        <h6 class="mb-3">Productos:</h6>
                        <div class="row">
                            @foreach($order->items as $item)
                                <div class="col-md-6 mb-3">
                                    <div class="order-item">
                                        <div class="d-flex align-items-center">
                                            @if($item->product->image_url)
                                                <img src="{{ $item->product->image_url }}"
                                                     alt="{{ $item->product->name }}"
                                                     class="order-item-image">
                                            @else
                                                <div class="order-item-image d-flex align-items-center justify-content-center bg-light">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                            <div class="order-item-info">
                                                <h6 class="item-name">{{ $item->product->name }}</h6>
                                                <p class="item-details">
                                                    Cantidad: {{ $item->quantity }} | 
                                                    Precio: ${{ number_format($item->price, 2) }}
                                                </p>
                                                <p class="item-total">
                                                    <strong>Total: ${{ number_format($item->price * $item->quantity, 2) }}</strong>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if($order->shipping_address)
                        <div class="order-shipping">
                            <h6>Dirección de envío:</h6>
                            <p>{{ $order->shipping_address }}</p>
                            @if($order->phone)
                                <p><strong>Teléfono:</strong> {{ $order->phone }}</p>
                            @endif
                        </div>
                    @endif

                    @if($order->notes)
                        <div class="order-notes">
                            <h6>Notas:</h6>
                            <p>{{ $order->notes }}</p>
                        </div>
                    @endif

                    <div class="order-actions">
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-2"></i>Ver Detalles
                        </a>
                        @if($order->status === 'pending')
                            <button class="btn btn-outline-danger ms-2" onclick="confirmCancel({{ $order->id }})">
                                <i class="fas fa-times me-2"></i>Cancelar Pedido
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
        </div>
    @else
        <!-- No Orders -->
        <div class="empty-orders text-center py-5">
            <div class="empty-orders-icon mb-4">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <h3 class="mb-3">No tienes pedidos aún</h3>
            <p class="text-muted mb-4">¡Explora nuestros productos y realiza tu primer pedido!</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary-custom btn-lg">
                <i class="fas fa-search me-2"></i>Explorar Productos
            </a>
        </div>
    @endif
</div>

@push('styles')
<style>
    .orders-title {
        font-family: var(--font-display);
        font-size: 2.5rem;
        font-weight: 600;
        color: var(--primary-color);
    }
    
    .order-card {
        background: white;
        border: none;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }
    
    .order-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(0,0,0,0.12);
    }
    
    .order-header {
        border-bottom: 2px solid #f8f9fa;
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .order-number {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 0.25rem;
    }
    
    .order-date {
        color: var(--medium-gray);
        font-size: 0.9rem;
        margin-bottom: 0;
    }
    
    .order-total {
        font-size: 1.4rem;
        color: var(--primary-color);
    }
    
    .badge-status {
        font-size: 0.85rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
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
    
    .order-items h6 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 1rem;
    }
    
    .order-item {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1rem;
        height: 100%;
    }
    
    .order-item-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    
    .order-item-info {
        flex: 1;
    }
    
    .item-name {
        font-size: 1rem;
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 0.25rem;
    }
    
    .item-details {
        font-size: 0.85rem;
        color: var(--medium-gray);
        margin-bottom: 0.25rem;
    }
    
    .item-total {
        font-size: 0.9rem;
        margin-bottom: 0;
    }
    
    .order-shipping, .order-notes {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1rem;
        margin-top: 1rem;
    }
    
    .order-shipping h6, .order-notes h6 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .order-actions {
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid #e9ecef;
    }
    
    .empty-orders-icon {
        font-size: 5rem;
        color: var(--medium-gray);
    }
    
    .empty-orders h3 {
        color: var(--primary-color);
    }
    
    @media (max-width: 768px) {
        .orders-title {
            font-size: 2rem;
        }
        
        .order-card {
            padding: 1.5rem;
        }
        
        .order-header .col-md-6:last-child {
            margin-top: 1rem;
            text-align: left !important;
        }
        
        .order-item-image {
            width: 50px;
            height: 50px;
        }
        
        .item-name {
            font-size: 0.9rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function confirmCancel(orderId) {
        if (confirm('¿Estás seguro de que quieres cancelar este pedido?')) {
            // Aquí puedes agregar la lógica para cancelar el pedido
            // Por ejemplo, enviar una petición AJAX al servidor
            alert('Funcionalidad de cancelación pendiente de implementar');
        }
    }
</script>
@endpush
@endsection
