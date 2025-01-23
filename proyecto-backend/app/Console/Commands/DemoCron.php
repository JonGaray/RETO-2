<?php

namespace App\Console\Commands;

use App\Models\Incident;
use App\Models\Machine;
use App\Models\MachineMaintenance;
use App\Models\Maintenance;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DemoCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $machinesMaintenances = MachineMaintenance::whereDate('next_due_date', Carbon::today())->get();
        \Log::debug($machinesMaintenances);
        foreach ($machinesMaintenances as $machinesMaintenance) {
            $machine = Machine::find($machinesMaintenance->machines_id)->first();
            \Log::debug($machine);
            $maintenance = Maintenance::find($machinesMaintenance->maintenances_id)->first();
            \Log::debug($maintenance);
            if ($machine && $maintenance) {
                $incident = new Incident();
                $incident->title = "Mantenimiento de maquina {$machine->name}";
                $incident->description = "{$maintenance->name} para la máquina {$machine->name}";
                $incident->status = 'nuevo';
                $incident->importance = 'mantenimiento';
                $incident->machines_id = $machine->id;
                $incident->created_at = Carbon::now();
                $incident->next_due_date = Carbon::now()->addDays($maintenance->regularity);
                $incident->failuretypes_id = 7;
                $incident->save();
                $machinesMaintenance->next_due_date = Carbon::now()->addDays($maintenance->regularity);
                $machinesMaintenance->save();
            } else {
                \Log::error('Error: Relación de máquina o mantenimiento no encontrada para MachineMaintenance ID: ' . $machinesMaintenance->id);
                \Log::error('Maquina: ' . $machinesMaintenance->machine()->id);
                \Log::error('Mantenimiento: ' . $machinesMaintenance->maintenance()->id);
            }
        }
    }

}
