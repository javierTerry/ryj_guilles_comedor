<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DashboardReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $stats;
    public string $periodo;
    public ?string $notas;

    /**
     * Create a new message instance.
     */
    public function __construct(array $stats, string $periodo = 'Diario', ?string $notas = null)
    {
        $this->stats = $stats;
        $this->periodo = $periodo;
        $this->notas = $notas;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📊 Reporte de Estadísticas de Comedor - ' . now()->format('d/m/Y'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.dashboard_report',
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
