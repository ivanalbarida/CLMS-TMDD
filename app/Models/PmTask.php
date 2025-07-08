<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PmTask extends Model
{
    use HasFactory;
    protected $fillable = ['category', 'task_description', 'frequency', 'is_active'];

    public function completions()
    {
        return $this->hasMany(PmTaskCompletion::class);
    }
}