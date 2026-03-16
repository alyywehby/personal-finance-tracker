<?php
namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionService
{
    public function getFilteredQuery(int $userId, array $filters)
    {
        $query = Transaction::with('category')
            ->where('user_id', $userId)
            ->orderBy('transaction_date', 'desc');

        if (!empty($filters['category_id'])) {
            $query->byCategory((int) $filters['category_id']);
        }
        if (!empty($filters['type'])) {
            $query->byType($filters['type']);
        }
        if (!empty($filters['from']) || !empty($filters['to'])) {
            $query->byDateRange($filters['from'] ?? null, $filters['to'] ?? null);
        }

        return $query;
    }

    public function streamCsvExport(int $userId, array $filters): StreamedResponse
    {
        $locale = App::getLocale();
        $isAr = $locale === 'ar';

        $headers_row = $isAr
            ? ['التاريخ', 'الوصف', 'الفئة', 'النوع', 'المبلغ']
            : ['Date', 'Description', 'Category', 'Type', 'Amount'];

        $filename = 'transactions_' . now()->format('Y-m') . '.csv';

        return response()->streamDownload(function () use ($userId, $filters, $headers_row, $isAr) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8 compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, $headers_row);

            $this->getFilteredQuery($userId, $filters)
                ->cursor()
                ->each(function ($transaction) use ($handle, $isAr) {
                    fputcsv($handle, [
                        $transaction->transaction_date->format('Y-m-d'),
                        $transaction->description ?? '',
                        $transaction->category->name ?? '',
                        $isAr
                            ? ($transaction->type === 'income' ? 'دخل' : 'مصروف')
                            : ucfirst($transaction->type),
                        number_format((float) $transaction->amount, 2, '.', ''),
                    ]);
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
