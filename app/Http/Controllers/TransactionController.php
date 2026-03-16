<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(private TransactionService $transactionService) {}

    public function index(Request $request)
    {
        $user = auth()->user();
        $filters = $request->only(['category_id', 'type', 'from', 'to']);

        $transactions = $this->transactionService
            ->getFilteredQuery($user->id, $filters)
            ->paginate(15)
            ->withQueryString();

        $categories = $user->categories()->orderBy('name')->get();

        return view('transactions.index', compact('transactions', 'categories', 'filters'));
    }

    public function create()
    {
        $categories = auth()->user()->categories()->orderBy('name')->get();
        return view('transactions.create', compact('categories'));
    }

    public function store(StoreTransactionRequest $request)
    {
        auth()->user()->transactions()->create($request->validated());
        return redirect()->route('transactions.index')->with('success', __('app.success'));
    }

    public function edit(Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);
        $categories = auth()->user()->categories()->orderBy('name')->get();
        return view('transactions.edit', compact('transaction', 'categories'));
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);
        $transaction->update($request->validated());
        return redirect()->route('transactions.index')->with('success', __('app.success'));
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);
        $transaction->delete();
        return back()->with('success', __('app.success'));
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
