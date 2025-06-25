<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'user_id', // This will be the assigned technician
        'issue_description',
        'action_taken',
        'date_reported',
        'status',
        'date_completed',
    ];

    /**
     * Get the equipment that this maintenance record belongs to.
     */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Get the user (technician) assigned to this record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}