@extends('layouts.app')

@section('title', 'Administrar Productos - Edward Villa Perfumería')

@section('content')
<div class="admin-container">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="admin-title">Gestión de Productos</h1>
            <p class="text-muted mb-0">Administra el catálogo de productos de la tienda</p>
        </div>
        <a href="{{ route('admin.productos.create') }}" class="btn btn-primary-custom">
            <i class="fas fa-plus me-2"></i>Nuevo Producto
        </a>
    </div>

    <!-- Products Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Imagen</th>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>
                                        <img src="{{ $product->image_url }}" 
                                             alt="{{ $product->name }}" 
                                             class="admin-product-image">
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $product->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $product->size }}ml - {{ ucfirst($product->gender) }}</small>
                                        </div>
                                    </td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>${{ number_format($product->price, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $product->stock > 10 ? 'bg-success' : ($product->stock > 0 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ $product->stock }} unidades
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $product->active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $product->active ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('products.show', $product) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.productos.edit', $product) }}" 
                                               class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.productos.destroy', $product) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-box-open text-muted mb-3" style="font-size: 4rem;"></i>
                    <h4 class="text-muted">No hay productos registrados</h4>
                    <p class="text-muted">Comienza agregando tu primer producto al catálogo.</p>
                    <a href="{{ route('admin.productos.create') }}" class="btn btn-primary-custom">
                        <i class="fas fa-plus me-2"></i>Crear Primer Producto
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .admin-title {
        font-family: var(--font-display);
        font-size: 2.5rem;
        font-weight: 600;
        color: var(--primary-color);
    }
    
    .admin-product-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }
    
    .admin-product-placeholder {
        width: 60px;
        height: 60px;
        background-color: var(--light-gray);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--medium-gray);
    }
    
    .table th {
        font-weight: 600;
        color: var(--primary-color);
        border-bottom: 2px solid #dee2e6;
    }
    
    .btn-group .btn {
        margin-right: 2px;
    }
    
    .btn-group .btn:last-child {
        margin-right: 0;
    }
    
    @media (max-width: 768px) {
        .admin-title {
            font-size: 2rem;
        }
        
        .table-responsive {
            font-size: 0.9rem;
        }
        
        .admin-product-image,
        .admin-product-placeholder {
            width: 40px;
            height: 40px;
        }
    }
</style>
@endpush
@endsection
