<?php

namespace App\Models;

use App\Casts\JsonConvertCast;
use App\Models\Traits\HasVisibilityScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    use HasVisibilityScope;

    protected $fillable = [
        "title",
        "description",
        "image",
        "document",
        "visibility",
        "sortable",
        "service_category_id",
    ];
    protected $casts = [
        "title" => JsonConvertCast::class,
        "description" => JsonConvertCast::class,
    ];

    protected static function booted()
    {
        // If the name of the service is updated, update the service name field in tasks
        static::updated(function ($service) {
            $changes = $service->getChanges();
            if (array_key_exists('title', $changes)) {
                $service->tasks()->update(['service_name_snapshot' => $service->title->ka ?? $service->title->en]);
            }
        });
        // If the service is deleted, update the service field in tasks
        // static::deleting(function ($service) {
        //     $service->tasks()->update(['service_id' => null,]);
        // });

    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, "service_category_id");
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'responsible_person_service')->withTimestamps();
    }
}
