<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;


class BatteryProduction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'info',
        'tower_id',
        'battery_id',
        'amount',
        'installation_date',
        'removal_date',
        'active',
        'production_percentage'
    ];
    protected $casts = [
    'installation_date' => 'date',
    'removal_date' => 'date',
];


    public function tower()
    {
        return $this->belongsTo(Tower::class);
    }
        public function getYearsSinceInstallationAttribute()
    {
        if (!$this->installation_date) {
            return null;
        }

        $installationDate = Carbon::parse($this->installation_date);
        $now = Carbon::now();

        $years = (int) $installationDate->diffInYears($now);
        $months = 0;

        if ($years < 1) {
            $months = (int) $installationDate->diffInMonths($now);
            return $months . ' mês' . ($months !== 1 ? 'es' : '');
        } else {
            $months = (int) $installationDate->copy()->addYears($years)->diffInMonths($now);
            $parts = [];

            $parts[] = $years . ' ano' . ($years !== 1 ? 's' : '');

            if ($months > 0) {
                $parts[] = $months . ' mês' . ($months !== 1 ? 'es' : '');
            }

            return implode(' e ', $parts);
        }
    }


    public function battery()
    {
        return $this->belongsTo(Battery::class);
    }
    
}
