<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ADFMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leadData;
    /**
     * Create a new message instance.
     */
    public function __construct($leadData)
    {
        $this->leadData = $leadData;
    }

    /**
     * Get the message envelope.
     */

    public function build()
    {
        $adfContent = $this->generateADFContent();

        return $this->from(env('mail_from_address'), env('MAIL_FROM_NAME'))
                    ->subject('New ADF Lead Notification')
                    ->view('email.adf')
                    ->with(['adfContent' => $adfContent])
                    ->text('email.adf');
    }


    private function generateADFContent()
    {
        $leadData = $this->sanitizeLeadData($this->leadData);

        return <<<EOD
            <?xml version="1.0"?>
            <adf>
                <prospect>
                    <id source="bestdreamcar.com">{$leadData['id']}</id>
                    <vehicle>
                        <year>{$leadData['vehicle_year']}</year>
                        <make>{$leadData['vehicle_make']}</make>
                        <model>{$leadData['vehicle_model']}</model>
                    </vehicle>
                    <customer>
                        <contact>
                            <name part="full">{$leadData['customer_name']}</name>
                            <phone>{$leadData['customer_phone']}</phone>
                            <email>{$leadData['customer_email']}</email>
                        </contact>
                    </customer>
                </prospect>
            </adf>
            EOD;
    }

    private function sanitizeLeadData($leadData)
    {
        return array_map(function ($value) {
            if (is_array($value)) {
                // Remove null values and join array items into a string
                return implode(', ', array_filter($value, fn($item) => !is_null($item)));
            }
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); // Escape special characters
        }, $leadData);
    }

    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'A D F Mail',
    //     );
    // }

    // /**
    //  * Get the message content definition.
    //  */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'view.name',
    //     );
    // }

    // /**
    //  * Get the attachments for the message.
    //  *
    //  * @return array<int, \Illuminate\Mail\Mailables\Attachment>
    //  */
    // public function attachments(): array
    // {
    //     return [];
    // }
}
