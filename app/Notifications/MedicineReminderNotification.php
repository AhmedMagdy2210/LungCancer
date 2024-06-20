<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class MedicineReminderNotification extends Notification {
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private array $data) {
        //
    }
    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via() {
        return [OneSignalChannel::class];
    }

    public function toOneSignal() {
        return OneSignalMessage::create()
            ->setSubject("Medicine Reminder")
            ->setBody("It's Time to take your Medicine : ", $this->data);
    }
}
