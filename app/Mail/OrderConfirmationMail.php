<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $event;
    public $quantities;
    public $orderTotal;
    public $discountAmount;
    public $appliedDiscounts;
    public $subtotal;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->event = $order->event;
        $this->appliedDiscounts = $order->discountCodes()->get();

        $this->quantities = $this->event->ticketTypes->mapWithKeys(function ($ticket) {
            return [
                $ticket->id => (object) [
                    'name' => $ticket->name,
                    'price_cents' => $ticket->price_cents,
                    'amount' => $this->order->tickets
                        ->where('ticket_type_id', $ticket->id)
                        ->count()
                ]
            ];
        })->all();

        $this->subtotal = collect($this->quantities)->sum(function($ticket) {
            return $ticket->price_cents * $ticket->amount;
        });

        // Calculate discount amount
        $this->discountAmount = 0;
        if (!$this->appliedDiscounts->isEmpty()) {
            $sum_percentage = $this->appliedDiscounts->sum('discount_percent');
            $sum_fixed = $this->appliedDiscounts->sum('discount_fixed_cents');

            if (!empty($sum_percentage)) {
                $this->discountAmount = $this->subtotal * ($sum_percentage / 100);
            } else if (!empty($sum_fixed)) {
                $this->discountAmount = $sum_fixed;
            }
        }

        $this->orderTotal = max(0, $this->subtotal - $this->discountAmount);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = "Order confirmation for {$this->order->event->name}";

        if (!empty($this->event->venue)) {
            $subject .= " - {$this->event->venue->name}";
        }

        return new Envelope(
            subject: $subject
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order-confirmation',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
