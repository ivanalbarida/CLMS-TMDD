<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftwareChecklist extends Model
{
    use HasFactory;
    protected $fillable = [
        'program_name', 'year_and_sem', 'software_name', 'version', 'notes', 'user_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}