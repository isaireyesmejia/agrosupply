<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['tenant_id', 'name', 'sku', 'description', 'price', 'stock'];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }
}