<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action_type',
        'subject_id',
        'subject_type',
        'description',
        'properties',
    ];

    // Cast the properties column to an array/object
    protected $casts = [
        'properties' => 'array',
    ];

    // The polymorphic relationship to get the subject (e.g., the Equipment or Lab model)
    public function subject()
    {
        return $this->morphTo();
    }

    // The relationship to get the user who performed the action
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}