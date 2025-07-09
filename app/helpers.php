<?php

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

if (! function_exists('log_activity')) {
    function log_activity(string $actionType, Model $subject, string $description, ?array $properties = null)
    {
        ActivityLog::create([
            'user_id'      => Auth::id(),
            'action_type'  => $actionType,
            'subject_id'   => $subject->id,
            'subject_type' => get_class($subject),
            'description'  => $description,
            'properties'   => $properties,
        ]);
    }
}