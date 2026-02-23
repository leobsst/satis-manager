<?php

declare(strict_types=1);

namespace App\Notifications;

use Filament\Facades\Filament;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly string $token,
        public string $subject,
        public string $action,
        public string $l1,
        public ?string $l2 = null,
        public ?string $l3 = null,
        public ?string $l4 = null
    ) {}

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
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subject)
            ->greeting('Bonjour ' . "{$notifiable->name},")
            ->line($this->l1)
            ->line($this->l2)
            ->line($this->l3)
            ->line($this->l4)
            ->action($this->action, $this->resetUrl($notifiable))
            ->line('Ce lien de réinitialisation de mot de passe expirera dans 24 heures.')
            ->line('Si vous n\'avez fait aucune demande, aucune action n\'est requise.')
            ->salutation('Cordialement');
    }

    protected function resetUrl(mixed $notifiable): string
    {
        return Filament::getResetPasswordUrl($this->token, $notifiable);
    }
}
