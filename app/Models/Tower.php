<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tower extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'voltage'];

    public function batteryProductions()
    {
        return $this->hasMany(BatteryProduction::class);
    }

    public function activeBattery()
{
    return $this->hasOne(BatteryProduction::class, 'tower_id')->where('active', 'yes');
}


    public function equipmentProductions()
    {
        return $this->hasMany(EquipmentProduction::class);
    }

    public function plateProductions()
    {
        return $this->hasMany(PlateProduction::class);
    }

    public function summary()
    {
        return $this->hasOne(TowerSummaries::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    
    public function updateConsumptionAh()
    {
        // Soma os watts de todos os equipamentos ativos
        $AllWattsDay = $this->equipmentProductions()
            ->where('active', 'yes')
            ->join('equipments', 'equipment_productions.equipment_id', '=', 'equipments.id')
            ->sum('equipments.watts');

        $AllWattTime = $AllWattsDay/24;

        // Atualiza o campo consumption_ah_day no resumo da torre
        if ($this->summary) {
            $this->summary->update(['consumption_ah_day' => $AllWattsDay]);
            $this->summary->update(['time_ah_consumption' => $AllWattTime]);
        }

        // Pega o valor de horas de autonomia (option)
        $hoursAutonomy = \App\Models\Option::where('reference', 'hours_autonomy')->value('value') ?? 48;

        // Se existir o valor, atualiza o battery_required
        if ($hoursAutonomy) {
            $batteryRequired = $this->summary->time_ah_consumption * $hoursAutonomy;

            $this->summary->update(['battery_required' => $batteryRequired]);
        }
    }

    public function updatePlate()
    {
        // Soma os watts das placas associadas a esta torre específica
        $totalWatts= \DB::table('plate_productions')
            ->join('plates', 'plate_productions.plate_id', '=', 'plates.id')
            ->where('plate_productions.tower_id', $this->id)
            ->whereNull('plates.deleted_at')                 // ignora placas deletadas
            ->whereNull('plate_productions.deleted_at')      // ignora vínculos deletados
            ->sum('plates.watts');
    
        // Soma os amps das placas associadas a esta torre específica
        $totalAmps= \DB::table('plate_productions')
            ->join('plates', 'plate_productions.plate_id', '=', 'plates.id')
            ->where('plate_productions.tower_id', $this->id)
            ->whereNull('plates.deleted_at')                 // ignora placas deletadas
            ->whereNull('plate_productions.deleted_at')      // ignora vínculos deletados
            ->sum('plates.amps');
        // Atualiza o campo watts_plate no resumo técnico da torre
        if ($this->summary) {
            $this->summary->update(['watts_plate' => $totalWatts]);
            $this->summary->update(['amps_plate' => $totalAmps]);
        }
            // Retorna os valores atualizados para log
    return [
        'totalWatts' => $totalWatts,
        'totalAmps'  => $totalAmps,
    ];
    
}
    


}
