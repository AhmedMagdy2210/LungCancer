<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Medicine;
use App\Models\UserMedicine;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use App\Events\MedicineReminderEvent;

class MedicineReminderCommand extends Command {
    protected $signature = 'medicine:reminders';

    protected $description = 'Check and remind users about medicines.';

    public function handle() {
        $now = Carbon::now();
        $userMedicines = UserMedicine::with('user', 'medicine')
            ->where('time', '<=', $now)
            ->get();

        foreach ($userMedicines as $userMedicine) {
            $nextTime = Carbon::parse($userMedicine->start_time);
            while ($nextTime <= $now) {
                $nextTime->addHours($userMedicine->duration);
            }

            if ($nextTime->subHours($userMedicine->duration)->diffInMinutes($now) < 15) {
                // Send notification if within 15 minutes window
                $this->sendNotification($userMedicine);
            }
        }
        return 0;
    }

    public function sendNotification($userMedicine) {
        $user = $userMedicine->user;
        $medicine = $userMedicine->medicine;
        $userToSend = User::where('id', $user->id)->first();
        dd($userToSend);
    }
}
