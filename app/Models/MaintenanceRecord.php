<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'user_id',
        'issue_description',
        'action_taken',
        'date_reported',
        'scheduled_for',
        'date_started', 
        'status',
        'date_completed',
        'category',
    ];

    /**
     * Get the equipment that this maintenance record belongs to.
     */
    public function equipment()
    {
        return $this->belongsToMany(Equipment::class, 'equipment_maintenance');
    }

    /**
     * Get the user (technician) assigned to this record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}