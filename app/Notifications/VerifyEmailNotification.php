<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    /**
     * The user instance.
     *
     * @var User
     */
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)

            ->subject('メールアドレスの確認')
            ->greeting('こんにちは！ ' . $this->user->name . 'さん!')
            ->line('ご登録ありがとうございます！Serori.Rです！')
            ->line('この度は、HairSalonApp にご登録いただき、誠にありがとうございます！')
            ->line('以下のボタンをクリックしてメールアドレスを承認してください。')
            ->action('メールアドレスを承認する', $this->verificationUrl($notifiable))
            ->line('もし心当たりがない場合は、このメールを無視してください。');
    }


    /**
     * Get the verification URL for the given notifiable.
     *
     * @param mixed $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        // 署名付きURLを生成する
        $signedUrl = URL::temporarySignedRoute(
            'verification.verifyEmail', // ルート名
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)), // リンクの有効期限
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ] // パラメータ
        );
        Log::info('signedUrl: ' . $signedUrl);

        // 署名付きURLをそのまま返す
        return  $signedUrl;
    }
    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
