<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleMaintenanceFile extends Model
{
    protected $table = 'vehicle_maintenance_files';
    use SoftDeletes;

    protected $fillable = [
        'vehicle_maintenance_id',
        'original_name',
        'file_name',
        'path',
        'mime_type',
        'size',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    public function maintenance(): BelongsTo
    {
        return $this->belongsTo(
            VehicleMaintenance::class,
            'vehicle_maintenance_id'
        );
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }

    public function getFormattedSizeAttribute(): string
    {
        if (!$this->size) {
            return '-';
        }

        $units = ['B', 'KB', 'MB', 'GB'];

        $size = $this->size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return number_format($size, 2, ',', '.') . ' ' . $units[$unit];
    }
    protected static function booted(): void
    {
        static::creating(function ($file) {
            $file->token = (string) \Illuminate\Support\Str::uuid();
        });
    }
}
