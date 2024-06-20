<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CancerDetectionResult extends Model {
    use HasFactory;
    protected $fillable = ['user_id', 'detection_time', 'result_details', 'confidence_level'];

    public function patient() {
        return $this->belongsTo(User::class);
    }
}
