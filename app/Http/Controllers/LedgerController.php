<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LedgerEntry;
use App\Models\Customer;
use App\Models\Vendor;
use PDF;
use Carbon\Carbon;

class LedgerController extends Controller
{
    // ... existing methods ...

    /**
     * Apply filters to the Ledger query.
     */
    private function applyFilters($query, Request $request)
    {
        // Filter by ledgerable type (Customer or Vendor)
        if ($request->filled('ledgerable_type')) {
            $ledgerableType = $request->ledgerable_type === 'customer' ? 'App\Models\Customer' : 'App\Models\Vendor';
            $query->where('ledgerable_type', $ledgerableType);
        }

        // Filter by specific ledgerable entity (Customer ID or Vendor ID)
        if ($request->filled('ledgerable_id')) {
            $query->where('ledgerable_id', $request->ledgerable_id);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', Carbon::parse($request->start_date)->startOfDay());
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', Carbon::parse($request->end_date)->endOfDay());
        }

        // Filter by transaction type
        if ($request->filled('transaction_type')) {
            $query->whereHas('transaction', function ($q) use ($request) {
                $q->where('type', $request->transaction_type);
            });
        }

        return $query;
    }

    /**
     * Display a listing of the ledgers with filters.
     */
    public function index(Request $request)
    {
        // Initialize query
        $query = LedgerEntry::query();

        // Apply filters
        $query = $this->applyFilters($query, $request);

        // Pagination
        $ledgers = $query->with(['ledgerable', 'transaction'])->orderBy('date', 'desc')->paginate(25);

        // Fetch customers and vendors for filter dropdowns
        $customers = Customer::all();
        $vendors = Vendor::all();

        if ($request->ajax()) {
            return view('ledgers.table', compact('ledgers'))->render();
        }

        return view('ledgers.index', compact('ledgers', 'customers', 'vendors'));
    }

    /**
     * Generate a PDF of the filtered ledgers.
     */
    public function generatePDF(Request $request)
    {
        // Validate required fields
        $request->validate([
            'ledgerable_type' => 'required|in:customer,vendor',
            'ledgerable_id' => 'required_if:ledgerable_type,customer|required_if:ledgerable_type,vendor',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date', // Ensure end date is not before start date
        ]);

        // Initialize query
        $query = LedgerEntry::query();

        // Apply filters
        $query = $this->applyFilters($query, $request);

        // Get all filtered data without pagination
        $ledgers = $query->with(['ledgerable', 'transaction'])->orderBy('date', 'asc')->get();

        // Prepare data for the view
        $filters = $request->only(['ledgerable_type', 'ledgerable_id', 'start_date', 'end_date', 'transaction_type']);

        // Load the PDF view
        $pdf = PDF::loadView('ledgers.pdf', compact('ledgers', 'filters'));

        // Return the PDF for inline display in browser
        return $pdf->stream('ledger_report.pdf'); // Changed from download() to stream()
    }
}
