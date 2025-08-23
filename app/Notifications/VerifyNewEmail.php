<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyNewEmail extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $email_exp_minute = 60;

        $url = URL::temporarySignedRoute(
            'email.change.verify',
            now()->addMinutes($email_exp_minute),
            ['id' => $notifiable->id, 'email' => $notifiable->pending_email]
        );

        return (new MailMessage)
            ->subject('メールアドレス変更の確認')
            ->line('以下のリンクをクリックして新しいメールアドレスを確認してください。')
            ->action('メールアドレスを確認', $url)
            ->line('このリンクは' . $email_exp_minute . '分で無効になります。');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
