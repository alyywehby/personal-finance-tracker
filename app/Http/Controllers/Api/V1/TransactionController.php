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

        return response()->json([
            'success' => true,
            'data' => TransactionResource::collection($transactions)->response()->getData(true),
            'message' => '',
        ]);
    }

    public function store(StoreTransactionRequest $request)
    {
        $transaction = auth()->user()->transactions()->create($request->validated());
        $transaction->load('category');

        return response()->json([
            'success' => true,
            'data' => new TransactionResource($transaction),
            'message' => 'Transaction created successfully',
        ], 201);
    }

    public function show(Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);
        $transaction->load('category');

        return response()->json([
            'success' => true,
            'data' => new TransactionResource($transaction),
            'message' => '',
        ]);
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);
        $transaction->update($request->validated());
        $transaction->load('category');

        return response()->json([
            'success' => true,
            'data' => new TransactionResource($transaction),
            'message' => 'Transaction updated successfully',
        ]);
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);
        $transaction->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Transaction deleted successfully',
        ]);
    }

    public function export(Request $request)
    {
        $filters = $request->only(['category_id', 'type', 'from', 'to']);
        return $this->transactionService->streamCsvExport(auth()->id(), $filters);
    }

    private function authorizeTransaction(Transaction $transaction): void
    {
        if ($transaction->user_id !== auth()->id()) {
            abort(response()->json(['success' => false, 'message' => 'Forbidden', 'errors' => []], 403));
        }
    }
}
