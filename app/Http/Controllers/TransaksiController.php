<?php


namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Services\TransactionService;
use App\Http\Requests\StoreTransaksiRequest;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $transaksis = Transaksi::with(['dompet', 'kategori'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $transaksis
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransaksiRequest $request)
    {
        try {
            $transaksi = $this->transactionService->createTransaction(
                $request->user(),
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibuat',
                'data' => $transaksi
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat transaksi: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $transaksi = Transaksi::with(['dompet', 'kategori'])
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $transaksi
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreTransaksiRequest $request, string $id)
    {
        try {
            $transaksi = Transaksi::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $updated = $this->transactionService->updateTransaction(
                $transaksi,
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diupdate',
                'data' => $updated
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate transaksi: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $transaksi = Transaksi::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $this->transactionService->deleteTransaction($transaksi);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus transaksi: ' . $e->getMessage()
            ], 422);
        }
    }
}
