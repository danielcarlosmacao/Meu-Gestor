<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceMaintenance extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'date_maintenance', 'service_client_id',
        'maintenance', 'cost_enterprise', 'cost_client'
    ];

 protected $casts = [
        'date_maintenance' => 'date',
    ];

    public function serviceClient()
    {
        return $this->belongsTo(ServiceClient::class);
    }
}
