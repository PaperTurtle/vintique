<?php

namespace App\Mail;

use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\{Address, Attachment, Content, Envelope};
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * SendOrderConfirmation is a mailable class responsible for constructing and sending an order confirmation email.
 * It includes details about the user and the transaction associated with the order.
 */
class SendOrderConfirmation extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param User  $user               the user instance
     * @param array $transactionDetails details of the transaction
     */
    public function __construct(private User $user, private array $transactionDetails) {}

    /**
     * Get the message envelope.
     * Defines the sender and the subject of the email.
     *
     * @return Envelope returns an Envelope instance with sender and subject details
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('handpickd.shop@gmail.com', 'Handpickd'),
            subject: 'Order Confirmation',
        );
    }

    /**
     * Get the message content definition.
     * Determines the view and data to be used in the email content.
     *
     * @return Content returns a Content instance specifying the view and data for the email
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.send_order_confirmation',
            with: [
                'user' => $this->user,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment> returns an array of attachments
     */
    public function attachments(): array
    {
        $pdfPath = $this->generatePdf();

        return [
            Attachment::fromPath($pdfPath)
                ->as('order-confirmation.pdf')
                ->withMime('application/pdf'),
        ];
    }

    /**
     * Generates a PDF file using the dompdf library.
     *
     * @return string the file path of the generated PDF file
     */
    public function generatePdf(): string
    {
        try {
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('pdf.order_confirmation', [
                'transactionDetails' => $this->transactionDetails,
            ]);

            $pdfFilePath = storage_path('pdfs/order-confirmation-' . time() . '.pdf');

            $pdf->save($pdfFilePath);
        } catch (Exception $e) {
            Log::error('Error during PDF generation and saving: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
        }

        return $pdfFilePath;
    }
}
