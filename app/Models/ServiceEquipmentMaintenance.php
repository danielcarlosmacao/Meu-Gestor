<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceEquipmentMaintenance extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'date_maintenance', 'assistance', 'service_client_id',
        'equipment', 'erro', 'date_send', 'date_received',
        'solution', 'cost_enterprise', 'cost_client'
    ];
    protected $casts = [
    'date_maintenance' => 'date',
    'date_send' => 'date',
    'date_received' => 'date',
];


   public function serviceClient()
{
    return $this->belongsTo(ServiceClient::class);
}
}
