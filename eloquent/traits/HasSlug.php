<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasSlug
{
    /**
     * Column name for the 'slug' attribute.
     */
    protected function slugKey(): string
    {
        return 'slug';
    }

    /**
     * Get the sluggable column name for the model.
     */
    abstract protected function sluggable(): string;

    /**
     * Boot the trait.
     */
    protected static function bootHasSlug()
    {
        static::creating(function (Model $model) {
            if (! $model->slug) {
                $model->slug = static::generateUniqueSlug($model->name);
            }
        });
    }

    /**
     * Query scope for finding a model by its primary slug.
     */
    public function scopeWhereSlug(Builder $query, string $slug): Builder
    {
        return $query->where($this->slugKey(), $slug);
    }

    /**
     * Find a model by its slug.
     */
    public static function findBySlug(string $slug, array $columns = ['*']): static
    {
        return static::whereSlug($slug)->first($columns);
    }

    /**
     * Find a model by its slug.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function findBySlugOrFail(string $slug, array $columns = ['*']): static
    {
        return static::whereSlug($slug)->firstOrFail($columns);
    }

    /**
     * Generate a unique slug.
     */
    public static function generateUniqueSlug(?string $str): ?string
    {
        if (! $str) {
            return null;
        }

        $counter = 1;
        $strSlug = Str::slug($str);
        $slug = $strSlug;

        while (static::whereSlug($slug)->exists()) {
            $slug = $strSlug.'-'.$counter++;
        }

        return $slug;
    }
}
