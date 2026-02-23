@extends('layouts.app')

@section('title', 'Confirmar Pedido - Edward Villa Perfumería')
@section('description', 'Confirma los detalles de tu pedido antes de proceder al pago.')

@section('content')
<div class="checkout-container">
    <!-- Progress Steps -->
    <div class="checkout-progress mb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="progress-steps">
                        <a href="{{ route('cart.index') }}" class="step completed">
                            <div class="step-number">1</div>
                            <div class="step-label">Carrito</div>
                        </a>
                        <div class="step-line completed"></div>
                        <a href="{{ route('checkout.shipping') }}" class="step completed">
                            <div class="step-number">2</div>
                            <div class="step-label">Envío</div>
                        </a>
                        <div class="step-line completed"></div>
                        <div class="step active">
                            <div class="step-number">3</div>
                            <div class="step-label">Confirmar</div>
                        </div>
                        <div class="step-line"></div>
                        <div class="step">
                            <div class="step-number">4</div>
                            <div class="step-label">Pago</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h3 class="mb-0 fw-bold">
                        <i class="fas fa-clipboard-check me-2" style="color: var(--gold);"></i>
                        Confirmar Pedido
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Shipping Information -->
                    <div class="shipping-info mb-4">
                        <h5 class="section-title">
                            <i class="fas fa-map-marker-alt me-2"></i>Dirección de Envío
                        </h5>
                        <div class="info-box p-3 bg-light rounded">
                            <p class="mb-1"><strong>{{ session('checkout.shipping.first_name') }}</strong></p>
                            <p class="mb-1">{{ session('checkout.shipping.address') }}</p>
                            <p class="mb-1">{{ session('checkout.shipping.city_name') ?? session('checkout.shipping.city') }}, {{ session('checkout.shipping.state_name') ?? session('checkout.shipping.state') }}</p>
                            <p class="mb-1">{{ session('checkout.shipping.phone') }}</p>
                            <p class="mb-0">{{ session('checkout.shipping.email') }}</p>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="order-items mb-4">
                        <h5 class="section-title">
                            <i class="fas fa-shopping-bag me-2"></i>Productos
                        </h5>
                        @foreach($cartItems as $item)
                            <div class="order-item d-flex align-items-center py-3 border-bottom">
                                <div class="item-image me-3">
                                    <img src="{{ $item->product->image_url }}"
                                         alt="{{ $item->product->name }}"
                                         class="img-fluid rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                </div>
                                <div class="item-details flex-grow-1">
                                    <h6 class="mb-0">{{ $item->product->name }}</h6>
                                    <small class="text-muted">Cantidad: {{ $item->quantity }}</small>
                                </div>
                                <div class="item-price">
                                    @if($item->product->hasActiveOffer())
                                        <div class="d-flex flex-column align-items-end">
                                            <span class="fw-bold">${{ number_format($item->product->final_price * $item->quantity, 2) }}</span>
                                            <small class="text-muted text-decoration-line-through">
                                                ${{ number_format($item->product->price * $item->quantity, 2) }}
                                            </small>
                                        </div>
                                    @else
                                        <span class="fw-bold">${{ number_format($item->product->price * $item->quantity, 2) }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Order Summary -->
                    <div class="order-summary">
                        <h5 class="section-title">
                            <i class="fas fa-calculator me-2"></i>Resumen del Pedido
                        </h5>
                        <div class="summary-box p-3 bg-light rounded">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span class="fw-bold">${{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Envío</span>
                                <span class="{{ $shippingCost === 0 ? 'text-success' : '' }} fw-bold">
                                    {{ $shippingCost === 0 ? 'Gratis' : '$' . number_format($shippingCost, 2) }}
                                </span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">Total</span>
                                <span class="fw-bold fs-5 text-primary">${{ number_format($total, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="actions mt-4 d-flex justify-content-between">
                        <a href="{{ route('checkout.shipping') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                        <form action="{{ route('checkout.process') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="fas fa-credit-card me-2"></i>Proceder al Pago
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .section-title {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--gold);
        display: inline-block;
    }

    .info-box, .summary-box {
        background-color: #f8f9fa;
        border-radius: 8px;
    }

    .order-item:last-child {
        border-bottom: none !important;
    }

    /* Progress Steps */
    .progress-steps {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 2rem;
        padding: 1rem;
        background: white;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        flex: 1;
        text-align: center;
        padding: 0 1rem;
        text-decoration: none;
        cursor: pointer;
    }

    .step:hover {
        text-decoration: none;
    }

    .step.completed:hover .step-number {
        transform: scale(1.1);
    }

    .step.completed:hover .step-label {
        color: var(--primary-color);
    }

    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #e9ecef;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
        position: relative;
        z-index: 2;
    }

    .step.active .step-number {
        background-color: var(--primary-color);
        color: white;
        transform: scale(1.1);
    }

    .step.completed .step-number {
        background-color: var(--gold);
        color: white;
    }

    .step-label {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 500;
        white-space: nowrap;
    }

    .step.active .step-label {
        color: var(--primary-color);
        font-weight: 600;
    }

    .step.completed .step-label {
        color: var(--gold);
        font-weight: 600;
    }

    .step-line {
        position: absolute;
        top: 20px;
        left: calc(50% + 30px);
        right: calc(50% - 30px);
        height: 2px;
        background-color: #e9ecef;
        z-index: 1;
    }

    .step-line.completed {
        background-color: var(--gold);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .checkout-container {
            padding: 0.5rem 0;
        }

        .checkout-progress {
            margin-bottom: 0.5rem;
        }

        /* Asegurar que el contenido no interfiera con la barra de búsqueda móvil */
        .main-content {
            margin-top: 140px !important;
        }

        .progress-steps {
            padding: 0.5rem;
            margin: 0;
            box-shadow: none;
            background: transparent;
        }

        .step {
            flex: 1;
            padding: 0 0.1rem;
            min-width: auto;
        }

        .step-number {
            width: 22px;
            height: 22px;
            font-size: 0.7rem;
            margin-bottom: 0.2rem;
        }

        .step-label {
            font-size: 0.65rem;
            line-height: 1;
        }

        .step-line {
            top: 11px;
            height: 1px;
            left: calc(50% + 15px);
            right: calc(50% - 15px);
        }

        .actions {
            flex-direction: column;
            gap: 0.5rem;
        }

        .actions .btn {
            width: 100%;
            padding: 0.5rem 1rem;
        }

        /* Ajustes adicionales para la página de confirmación */
        .card-header {
            padding: 0.75rem;
        }

        .card-body {
            padding: 0.75rem;
        }

        .info-box, .summary-box {
            padding: 0.75rem !important;
        }

        .section-title {
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .order-item {
            padding: 0.5rem 0;
        }

        .item-image img {
            width: 40px !important;
            height: 40px !important;
        }
    }
</style>
@endpush
@endsection
