<?php

namespace App\Notifications;

use App\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ItemPublished extends Notification
{
    use Queueable;

    private $comment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject(__(config('ui.frontend_layout') . '.notif_subject_item_published'))
                    ->greeting(__(config('ui.frontend_layout') . '.notif_greeting',
                        ['name' => $notifiable->name]
                    ))
                    ->line(__(config('ui.frontend_layout') . '.notif_item_published'))
                    ->line(__('notification.item_title', ['title' => $this->item->getTitleColumn()]))
                    ->action(__('items.my_own'), route('item.show.own'))
                    ->line(__(config('ui.frontend_layout') . '.notif_thanks'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
