<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tag_number',
        'lab_id',
        'status',
        'notes',
    ];

    /**
     * Get the components for the equipment.
     */
    public function components()
    {
        return $this->hasMany(Component::class);
    }

    /**
     * Get the lab that the equipment belongs to.
     */
    public function lab()
    {
        return $this->belongsTo(Lab::class);
    }
    
    public function maintenanceRecords()
    {
        return $this->hasMany(MaintenanceRecord::class);
    }
}