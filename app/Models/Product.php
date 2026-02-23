<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * Valores permitidos para el campo genero.
     * Reemplaza la restriccion del ENUM de MySQL para compatibilidad con PostgreSQL.
     */
    const GENEROS_VALIDOS = ['male', 'female', 'unisex'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'stock',
        'category_id',
        'size',
        'gender',
        'active'
    ];

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the cart items for the product.
     */
    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get the order items for the product.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the offers for the product.
     */
    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    /**
     * Get active offer for the product.
     */
    public function activeOffer()
    {
        return $this->offers()->active()->first();
    }

    /**
     * Get final price with discount if there's an active offer.
     */
    public function getFinalPriceAttribute()
    {
        $offer = $this->activeOffer();
        return $offer ? $offer->final_price : $this->price;
    }

    /**
     * Check if product has an active offer.
     */
    public function hasActiveOffer()
    {
        return $this->activeOffer() !== null;
    }

    /**
     * Get discount percentage if there's an active offer.
     */
    public function getDiscountPercentageAttribute()
    {
        $offer = $this->activeOffer();
        return $offer ? $offer->discount_percentage : 0;
    }

    /**
     * Obtiene la URL de la imagen del producto.
     * Si la imagen es una URL (Cloudinary), la devuelve directamente.
     * Si es un nombre de archivo (legacy), genera la URL local.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return asset('images/no-image.png'); // O una imagen por defecto
        }

        // Si ya es una URL completa (Cloudinary), devolverla directamente
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        // Caso legacy: Es un nombre de archivo, generar URL de storage local
        return asset('storage/products/' . $this->image);
    }
}
