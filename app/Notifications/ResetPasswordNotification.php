<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class ResetPasswordNotification extends Notification
{
    public $resetLink;


    public function __construct($resetLink)
    {
        $this->resetLink = $resetLink;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $resetUrl = env('FRONTEND_URL') . "/auth/resetPassword/" . $this->resetLink;

        return (new MailMessage)
            ->subject('パスワードリセットのメールです。')
            ->line('Serori.Rです！HairSalonAppのご利用ありがとうございます。')
            ->line('パスワードリセットのリクエストを受け付けました。')
            ->line('以下のボタンをクリックしてパスワードをリセットしてください。')
            ->action('パスワードをリセットする', $resetUrl)
            ->line('もし心当たりがない場合は、このメールを無視してください。');
    }
}
