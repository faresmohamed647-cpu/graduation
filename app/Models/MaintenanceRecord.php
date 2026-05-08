<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRecord extends Model
{
    use HasFactory;

    protected $fillable = ['bus_id', 'title', 'description', 'cost', 'status', 'maintenance_date'];

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }
}
