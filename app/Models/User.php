<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Chat;
use App\Models\Doctor;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\MessageSent;
use Spatie\Permission\Models\Role;
use App\Events\MedicineReminderEvent;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use App\Notifications\MedicineReminderNotification;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'specialization',
        'certification',
        'gender',
        'born_date',
        'national_id'
    ];

    public function roles() {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id');
    }


    public function chats(): HasMany {
        return $this->hasMany(Chat::class, 'created_by');
    }

    public function userMedicines() {
        return $this->hasMany(UserMedicine::class);
    }

    public function cancerResults() {
        return $this->hasMany(CancerDetectionResult::class);
    }
    public function routeNotificationForOneSignal() {
        return ['tags' => ['key' => 'userId', 'relation' => '=', 'value' => (string)(1)]];
        // return ['email' => 'dev.ahmedmagdy2002@gmail.com'];
    }
    public function sendNewMessageNotification(array $data) {
        $this->notify(new MessageSent($data));
    }
    public function sendMedicineReminderNotification(array $data) {
        $this->notify(new MedicineReminderNotification($data));
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
