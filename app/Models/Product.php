<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'product';
    protected $primaryKey = 'product_id';

    const CREATED_AT = 'product_createdat';
    const UPDATED_AT = 'product_updatedat';
    const DELETED_AT = 'product_deletedat';

    protected $fillable = [
        'product_name',
        'product_description',
        'product_price',
        'product_image',
        'product_status',
        'product_category',
        'product_featured',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'product_id');
    }
}