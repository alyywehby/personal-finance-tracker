<?php
namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MonthlySummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public array $summary,
        public array $topCategories,
        public int $year,
        public int $month
    ) {}

    public function envelope(): Envelope
    {
        $locale = $this->user->locale ?? 'en';
        $monthName = \Carbon\Carbon::create($this->year, $this->month)->locale($locale)->translatedFormat('F Y');

        $subject = $locale === 'ar'
            ? "ملخصك المالي لشهر {$monthName}"
            : "Your {$monthName} Financial Summary";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.monthly_summary',
            text: 'emails.monthly_summary_text',
        );
    }
}
