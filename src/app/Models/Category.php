<?php

namespace Fereydooni\Shopping\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'status',
        'sort_order',
        'is_default',
    ];

    // protected $casts = [
    //     'status' => \Fereydooni\Shopping\app\Enums\CategoryStatus::class,
    //     'is_default' => 'boolean',
    //     'created_at' => 'datetime',
    //     'updated_at' => 'datetime',
    // ];

    // public function parent(): BelongsTo
    // {
    //     return $this->belongsTo(Category::class, 'parent_id');
    // }

    // public function children(): HasMany
    // {
    //     return $this->hasMany(Category::class, 'parent_id');
    // }

    // public function products(): HasMany
    // {
    //     return $this->hasMany(Product::class);
    // }

    // public function allChildren(): HasMany
    // {
    //     return $this->children()->with('allChildren');
    // }

    // public function allParents()
    // {
    //     return $this->parent()->with('allParents');
    // }


}
