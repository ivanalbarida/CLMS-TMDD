<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PmTaskCompletion extends Model
{
    use HasFactory;
    protected $fillable = ['pm_task_id', 'user_id', 'lab_id', 'remarks', 'completed_at'];

    public function pmTask()
    {
        return $this->belongsTo(PmTask::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lab()
    {
        return $this->belongsTo(Lab::class);
    }
}