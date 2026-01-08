<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_item';
    protected $primaryKey = 'orderitem_id';
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'product_id',
        'orderitem_quantity',
        'orderitem_price',
        'orderitem_subtotal',
        'orderitem_notes',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}