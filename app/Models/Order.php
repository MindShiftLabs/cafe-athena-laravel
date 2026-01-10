<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $primaryKey = 'order_id';

    const CREATED_AT = 'order_createdat';
    const UPDATED_AT = 'order_updatedat';

    protected $fillable = [
        'user_id',
        'order_status',
        'order_type',
        'order_total',
        'order_payment_method',
        'order_payment_status',
        'order_notes',
        'order_delivery_address',
        'order_completedat',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id')->withTrashed();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }
}