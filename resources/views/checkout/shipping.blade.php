@extends('layouts.app')

@section('title', 'Información de Envío - Edward Villa Perfumería')
@section('description', 'Completa tu información de envío para proceder con tu pedido.')

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
                        <div class="step active">
                            <div class="step-number">2</div>
                            <div class="step-label">Envío</div>
                        </div>
                        <div class="step-line"></div>
                        <div class="step">
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

    <div class="row">
        <!-- Shipping Form -->
        <div class="col-lg-8">
            <div class="shipping-form-container">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0">
                        <h3 class="mb-0 fw-bold">
                            <i class="fas fa-shipping-fast me-2" style="color: var(--gold);"></i>
                            Información de Envío
                        </h3>
                        <p class="text-muted mb-0 mt-2">Completa los datos para el envío de tu pedido</p>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('checkout.shipping.process') }}" method="POST" id="shipping-form">
                            @csrf
                            
                            <!-- Personal Information -->
                            <div class="form-section mb-4">
                                <h5 class="section-title">
                                    <i class="fas fa-user me-2"></i>Información Personal
                                </h5>
                                
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="first_name" class="form-label">Nombre Completo *</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" 
                                           value="{{ old('first_name', $shippingData['first_name'] ?? auth()->user()->name ?? '') }}" required>
                                    @error('first_name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Correo Electrónico *</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="{{ old('email', $shippingData['email'] ?? auth()->user()->email ?? '') }}" required>
                                        @error('email')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Teléfono *</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" 
                                               value="{{ old('phone', $shippingData['phone'] ?? '') }}" required>
                                        @error('phone')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Shipping Address -->
                            <div class="form-section mb-4">
                                <h5 class="section-title">
                                    <i class="fas fa-map-marker-alt me-2"></i>Dirección de Envío
                                </h5>
                                
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label for="address" class="form-label">Dirección *</label>
                                        <input type="text" class="form-control" id="address" name="address" 
                                               value="{{ old('address', $shippingData['address'] ?? '') }}" placeholder="Calle, número, apartamento" required>
                                        @error('address')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="postal_code" class="form-label">Código Postal</label>
                                        <input type="text" class="form-control" id="postal_code" name="postal_code" 
                                               value="{{ old('postal_code', $shippingData['postal_code'] ?? '') }}">
                                        @error('postal_code')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="state" class="form-label">Departamento *</label>
                                        <select class="form-select" id="state" name="state" required>
                                            <option value="">Selecciona un departamento</option>
                                        </select>
                                        @error('state')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="city" class="form-label">Ciudad/Municipio *</label>
                                        <select class="form-select" id="city" name="city" required disabled>
                                            <option value="">Selecciona primero un departamento</option>
                                        </select>
                                        @error('city')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                        <div id="shipping-info" class="text-success mt-2" style="display: none;">
                                            <i class="fas fa-truck me-1"></i> <span id="shipping-message"></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3" style="display:none;">
                                    <label for="country" class="form-label">País *</label>
                                    <select class="form-select" id="country" name="country" required>
                                        <option value="CO" selected>Colombia</option>
                                    </select>
                                    @error('country')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Additional Notes -->
                            <div class="form-section mb-4">
                                <h5 class="section-title">
                                    <i class="fas fa-sticky-note me-2"></i>Notas Adicionales
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Instrucciones especiales de entrega (opcional)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" 
                                              placeholder="Ej: Dejar en portería, tocar timbre, etc.">{{ old('notes', $shippingData['notes'] ?? '') }}</textarea>
                                    @error('notes')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="form-actions d-flex justify-content-between">
                                <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-arrow-left me-2"></i>Volver al Carrito
                                </a>
                                <button type="submit" class="btn btn-primary-custom btn-lg">
                                    <i class="fas fa-arrow-right me-2"></i>Continuar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('styles')
<style>
    .checkout-container {
        padding: 2rem 0;
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

    /* Optimización para móvil - pasos más compactos */
    @media (max-width: 768px) {
        .progress-steps {
            padding: 0.5rem;
            margin-bottom: 1rem;
        }

        .step {
            flex: 1;
            padding: 0 0.25rem;
        }

        .step-number {
            width: 30px;
            height: 30px;
            font-size: 0.8rem;
            margin-bottom: 0.25rem;
        }

        .step-label {
            font-size: 0.7rem;
            white-space: nowrap;
        }

        .step-line {
            top: 15px;
            left: calc(50% + 20px);
            right: calc(50% - 20px);
            height: 1px;
        }
    }

    @media (max-width: 480px) {
        .progress-steps {
            padding: 0.25rem;
        }

        .step {
            padding: 0 0.1rem;
        }

        .step-number {
            width: 25px;
            height: 25px;
            font-size: 0.7rem;
        }

        .step-label {
            font-size: 0.6rem;
        }

        .step-line {
            top: 12px;
            left: calc(50% + 15px);
            right: calc(50% - 15px);
        }
    }

    /* Form Sections */
    .form-section {
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 1.5rem;
    }

    .form-section:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .section-title {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--gold);
        display: inline-block;
    }

    .form-label {
        font-weight: 600;
        color: var(--dark-gray);
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
    }

    /* Order Summary */
    .order-item {
        padding: 0.75rem 0;
        border-bottom: 1px solid #f8f9fa;
    }

    .order-item:last-child {
        border-bottom: none;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .summary-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .shipping-notice {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        border-left: 4px solid var(--primary-color);
    }

    .security-item {
        padding: 1rem 0.5rem;
    }

    .security-item i {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
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

        .form-actions {
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-actions .btn {
            width: 100%;
            padding: 0.5rem 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stateSelect = document.getElementById('state');
        const citySelect = document.getElementById('city');
        const shippingInfo = document.getElementById('shipping-info');
        const shippingMessage = document.getElementById('shipping-message');
        
        // Datos guardados en sesión
        const savedState = @json($shippingData['state'] ?? null);
        const savedCity = @json($shippingData['city'] ?? null);
        
        // Cargar departamentos al inicializar
        loadDepartments();
        
        // Evento para cargar municipios cuando se selecciona un departamento
        stateSelect.addEventListener('change', function() {
            const departmentCode = this.value;
            if (departmentCode) {
                loadCities(departmentCode);
                citySelect.disabled = false;
            } else {
                citySelect.innerHTML = '<option value="">Selecciona primero un departamento</option>';
                citySelect.disabled = true;
                hideShippingInfo();
            }
        });
        
        // Evento para calcular envío cuando se selecciona una ciudad
        citySelect.addEventListener('change', function() {
            const cityName = this.options[this.selectedIndex].text;
            const stateName = stateSelect.options[stateSelect.selectedIndex].text;
            calculateShipping(cityName, stateName);
        });
        
        async function loadDepartments() {
            try {
                const response = await fetch('https://api-colombia.com/api/v1/Department');
                const departments = await response.json();
                
                stateSelect.innerHTML = '<option value="">Selecciona un departamento</option>';
                // Ordenar departamentos alfabéticamente
                departments.sort((a, b) => a.name.localeCompare(b.name));
                
                departments.forEach(dept => {
                    const option = document.createElement('option');
                    option.value = dept.id;
                    option.textContent = dept.name;
                    // Preseleccionar si hay datos guardados
                    if (savedState && dept.id == savedState) {
                        option.selected = true;
                    }
                    stateSelect.appendChild(option);
                });
                
                // Si hay un departamento preseleccionado, cargar sus ciudades
                if (savedState) {
                    loadCities(savedState);
                    citySelect.disabled = false;
                }
            } catch (error) {
                console.error('Error cargando departamentos:', error);
                stateSelect.innerHTML = '<option value="">Error cargando departamentos</option>';
            }
        }
        
        async function loadCities(departmentId) {
            try {
                citySelect.innerHTML = '<option value="">Cargando ciudades...</option>';
                const response = await fetch(`https://api-colombia.com/api/v1/Department/${departmentId}/cities`);
                const cities = await response.json();
                
                citySelect.innerHTML = '<option value="">Selecciona una ciudad/municipio</option>';
                // Ordenar ciudades alfabéticamente
                cities.sort((a, b) => a.name.localeCompare(b.name));
                
                cities.forEach(city => {
                    const option = document.createElement('option');
                    option.value = city.id;
                    option.textContent = city.name;
                    // Preseleccionar si hay datos guardados
                    if (savedCity && city.id == savedCity) {
                        option.selected = true;
                        // Calcular envío para la ciudad preseleccionada
                        const stateName = stateSelect.options[stateSelect.selectedIndex].text;
                        calculateShipping(city.name, stateName);
                    }
                    citySelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error cargando ciudades:', error);
                citySelect.innerHTML = '<option value="">Error cargando ciudades</option>';
            }
        }
        
        function calculateShipping(cityName, stateName) {
            let isFreeShipping = false;
            let shippingCost = 17000;
            let message = '';
            
            // Normalizar nombres para comparación
            const normalizedCity = cityName.toLowerCase().trim();
            const normalizedState = stateName.toLowerCase().trim();
            
            // Gratis: La Paz (Cesar), San Diego (Cesar) o cualquier municipio de La Guajira
            if ((normalizedCity === 'la paz' || normalizedCity === 'san diego') && normalizedState === 'cesar') {
                isFreeShipping = true;
                shippingCost = 0;
                message = `¡Envío gratis a ${cityName}!`;
            } else if (normalizedState === 'la guajira') {
                isFreeShipping = true;
                shippingCost = 0;
                message = '¡Envío gratis a La Guajira!';
            // $7.000: Valledupar o Manaure
            } else if (normalizedCity === 'valledupar' || normalizedCity === 'manaure') {
                shippingCost = 7000;
                message = `Costo de envío a ${cityName}: $7.000`;
            } else {
                message = 'Costo de envío: $17.000';
            }
            
            showShippingInfo(message, isFreeShipping);
            updateOrderSummary(isFreeShipping, shippingCost);
        }
        
        function showShippingInfo(message, isFree) {
            shippingMessage.textContent = message;
            shippingInfo.className = isFree ? 'text-success mt-2' : 'text-warning mt-2';
            shippingInfo.style.display = 'block';
        }
        
        function hideShippingInfo() {
            shippingInfo.style.display = 'none';
        }
        
        function updateOrderSummary(isFreeShipping, shippingCost) {
            shippingCost = shippingCost ?? (isFreeShipping ? 0 : 17000);
            const shippingDisplay = shippingCost === 0 ? 'Gratis' : '$' + shippingCost.toLocaleString('es-CO');
            const shippingClass = shippingCost === 0 ? 'text-success fw-bold' : 'fw-bold';
            // Verificar si existe el resumen del pedido en esta página
            const summaryRows = document.querySelectorAll('.summary-row');
            const totalRow = document.querySelector('.summary-total');
            
            // Solo actualizar si existen los elementos del resumen
            if (summaryRows.length > 1) {
                const shippingRow = summaryRows[1]; // Segunda fila es el envío
                
                if (shippingRow) {
                    const shippingSpan = shippingRow.querySelector('span:last-child');
                    if (shippingSpan) {
                        if (isFreeShipping) {
                            shippingSpan.textContent = 'Gratis';
                            shippingSpan.className = 'text-success fw-bold';
                        } else {
                            shippingSpan.textContent = '$17.000';
                            shippingSpan.className = 'fw-bold';
                        }
                    }
                }
                
                // Recalcular total solo si existen los elementos necesarios
                if (summaryRows[0]) {
                    const subtotalElement = summaryRows[0].querySelector('span:last-child');
                    if (subtotalElement) {
                        const subtotalText = subtotalElement.textContent.replace(/[$,]/g, '');
                        const subtotal = parseFloat(subtotalText);
                        const shippingCost = isFreeShipping ? 0 : 17000;
                        const total = subtotal + shippingCost;
                        
                        if (totalRow) {
                            const totalSpan = totalRow.querySelector('span:last-child');
                            if (totalSpan) {
                                totalSpan.textContent = '$' + total.toLocaleString('es-CO', {minimumFractionDigits: 2});
                            }
                        }
                    }
                }
            }
            
            // Guardar información de envío en sessionStorage para usar en otras páginas
            sessionStorage.setItem('shippingInfo', JSON.stringify({
                isFreeShipping: isFreeShipping,
                cost: isFreeShipping ? 0 : 17000,
                message: shippingMessage.textContent
            }));
        }
        
        // Form validation
        const form = document.getElementById('shipping-form');
        
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Por favor, completa todos los campos requeridos.');
            } else {
                // Protección contra doble envío
                const btn = form.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
                }
            }
        });
        
        // Remove invalid class on input
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });
    });
</script>
@endpush
@endsection
