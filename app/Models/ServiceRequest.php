<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_id', 'technician_id', 'requesting_office', 'request_type', 'title',
        'description', 'equipment_details', 'classification', 'action_taken', 'recommendation',
        'status_after_service', 'client_verifier_name', 'status', 'started_at', 'completed_at',
        'rejection_reason',
    ];

    protected $casts = ['started_at' => 'datetime', 'completed_at' => 'datetime'];

    public function requester() {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function technician() {
        return $this->belongsTo(User::class, 'technician_id');
    }
}