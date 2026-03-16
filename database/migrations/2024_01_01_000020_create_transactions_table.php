<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['income', 'expense']);
            $table->decimal('amount', 10, 2);
            $table->string('description')->nullable();
            $table->date('transaction_date');
            $table->timestamps();
            $table->index('user_id');
            $table->index('category_id');
            $table->index('transaction_date');
            $table->index('type');
        });
    }
    public function down(): void {
        Schema::dropIfExists('transactions');
    }
};
