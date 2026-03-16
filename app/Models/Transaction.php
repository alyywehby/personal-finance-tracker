<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model {
    use HasFactory;

    protected $fillable = ['user_id', 'category_id', 'type', 'amount', 'description', 'transaction_date'];

    protected function casts(): array {
        return [
            'transaction_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function scopeByMonth(Builder $query, int $year, int $month): Builder {
        return $query->whereYear('transaction_date', $year)->whereMonth('transaction_date', $month);
    }

    public function scopeByType(Builder $query, string $type): Builder {
        return $query->where('type', $type);
    }

    public function scopeByCategory(Builder $query, int $categoryId): Builder {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByDateRange(Builder $query, ?string $from, ?string $to): Builder {
        if ($from) $query->where('transaction_date', '>=', $from);
        if ($to) $query->where('transaction_date', '<=', $to);
        return $query;
    }
}
