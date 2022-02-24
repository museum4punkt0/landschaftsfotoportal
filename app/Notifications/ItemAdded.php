<?php

namespace App\Notifications;

use App\Item;
use App\ItemRevision;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ItemAdded extends Notification
{
    use Queueable;

    private $item;

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
        if ($this->item instanceof ItemRevision) {
            $url = route('revision.show', $this->item);
            $title = $this->item->item->getTitleColumn();
        }
        else {
            $url = route('item.show', $this->item);
            $title = $this->item->getTitleColumn();
        }

        return (new MailMessage)
                    ->subject(__('notification.subject_topic') . " " . __('items.new'))
                    ->line(__('notification.item_added', ['user' => $this->item->creator->name]))
                    ->line(__('notification.item_title', ['title' => $title]))
                    ->action(__('common.show'), $url)
                    ->line(__('notification.please_review'));
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
