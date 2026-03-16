<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(private TransactionService $transactionService) {}

    public function index(Request $request)
    {
        $filters = $request->only(['category_id', 'type', 'from', 'to']);
        $perPage = min((int) ($request->per_page ?? 15), 100);

        $transactions = $this->transactionService
            ->getFilteredQuery(auth()->id(), $filters)
            ->paginate($perPage);

        return $this->apiResponse(
            TransactionResource::collection($transactions)->response()->getData(true)
        );
    }

    public function store(StoreTransactionRequest $request)
    {
        $transaction = auth()->user()->transactions()->create($request->validated());
        $transaction->load('category');

        return $this->apiResponse(
            new TransactionResource($transaction),
            'Transaction created successfully',
            201
        );
    }

    public function show(Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);
        $transaction->load('category');

        return $this->apiResponse(new TransactionResource($transaction));
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);
        $transaction->update($request->validated());
        $transaction->load('category');

        return $this->apiResponse(new TransactionResource($transaction), 'Transaction updated successfully');
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);
        $transaction->delete();

        return $this->apiResponse(message: 'Transaction deleted successfully');
    }

    public function export(Request $request)
    {
        $filters = $request->only(['category_id', 'type', 'from', 'to']);
        return $this->transactionService->streamCsvExport(auth()->id(), $filters);
    }

    private function authorizeTransaction(Transaction $transaction): void
    {
        if ($transaction->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
