<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Type;
use App\Models\Area;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Retrieve all types for the filter dropdown
        $types = Type::orderBy('name')->get();

        // Initialize the query
        $query = Customer::query();

        // Apply filters based on the request inputs

        // Filter by Name (partial match)
        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        // Filter by Email (exact match or partial)
        if ($request->filled('email')) {
            $query->where('email', 'LIKE', '%' . $request->email . '%');
        }

        // Filter by Phone (exact match or partial)
        if ($request->filled('phone')) {
            $query->where('phone', 'LIKE', '%' . $request->phone . '%');
        }

        // Filter by WhatsApp (exact match or partial)
        if ($request->filled('whatsapp')) {
            $query->where('whatsapp', 'LIKE', '%' . $request->whatsapp . '%');
        }

        // Filter by Type (exact match)
        if ($request->filled('type_id')) {
            $query->where('type_id', $request->type_id);
        }

        // Filter by Created At Date Range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date);
        }

        // Order by created_at in descending order
        $customers = $query->orderBy('created_at', 'desc')->paginate(50)->withQueryString();

        return view('customers.index', compact('customers', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = Type::all();
        $areas = Area::orderBy('name')->get();
        return view('customers.create', compact('types', 'areas'));
    }


    public function store(Request $request)
    {
        // Validation rules
        $request->validate([
            'name' => 'required|string|max:255|unique:customers,name',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'type_id' => 'nullable|exists:types,id',
            'area_id' => 'nullable|exists:areas,id',
            'opening_balance' => 'nullable|numeric|min:0',
            'opening_balance_type' => 'nullable|in:debit,credit',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            // Generate a slug for the customer name
            $slug = Str::slug($request->name);

            // Get the extension of the uploaded image
            $extension = $request->file('image')->getClientOriginalExtension();

            // Ensure the slug is unique
            while (Storage::disk('public')->exists('customers/' . $slug . '.' . $extension)) {
                $slug = $slug . '-' . Str::random(5);  // Append a random string to make it unique
            }

            // Generate a unique image name
            $imageName = $slug . '.' . $extension;

            // Store the image
            $data['image'] = $request->file('image')->storeAs('customers', $imageName, 'public');
        }

        $customer = Customer::create($data);

        // Add ledger entry if opening balance exists
        if (!empty($customer->opening_balance) && $customer->opening_balance > 0) {
            $debit = $customer->opening_balance_type === 'debit' ? $customer->opening_balance : 0;
            $credit = $customer->opening_balance_type === 'credit' ? $customer->opening_balance : 0;

            $ledgerEntry = new \App\Models\LedgerEntry([
                'transaction_id' => null,
                'date' => $customer->created_at ?? now(),
                'description' => 'Opening Balance',
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $debit - $credit,
                'user_id' => auth()->id(),
            ]);
            $ledgerEntry->ledgerable()->associate($customer);
            $ledgerEntry->save();
        }

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $types = Type::orderBy('name')->get();
        $areas = Area::orderBy('name')->get();
        return view('customers.edit', compact('customer', 'types', 'areas'));
    }

    /**
     * Update the specified Customer in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Customer $customer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Customer $customer)
    {
        // Validation rules
        $request->validate([
            'name' => 'required|string|max:255|unique:customers,name,' . $customer->id,
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'type_id' => 'nullable|exists:types,id',
            'area_id' => 'nullable|exists:areas,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        // Check if the name has changed and handle image renaming
        if ($request->name !== $customer->name && $customer->image) {
            // Generate slug for the new name
            $newSlug = Str::slug($request->name);
            $extension = pathinfo($customer->image, PATHINFO_EXTENSION);
            $newImageName = $newSlug . '.' . $extension;

            // Ensure the new image name is unique
            while (Storage::disk('public')->exists('customers/' . $newImageName)) {
                $newSlug = $newSlug . '-' . Str::random(5);
                $newImageName = $newSlug . '.' . $extension;
            }

            $newImagePath = 'customers/' . $newImageName;
            Storage::disk('public')->move($customer->image, $newImagePath);
            $data['image'] = $newImagePath;
        }

        // Handle new image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($customer->image) {
                Storage::disk('public')->delete($customer->image);
            }

            // Generate a new image name and store it
            $slug = Str::slug($request->name);
            $extension = $request->file('image')->getClientOriginalExtension();

            // Ensure the slug is unique
            while (Storage::disk('public')->exists('customers/' . $slug . '.' . $extension)) {
                $slug = $slug . '-' . Str::random(5);
            }

            $newImageName = $slug . '.' . $extension;
            $data['image'] = $request->file('image')->storeAs('customers', $newImageName, 'public');
        }

        // Update the customer
        $customer->update($data);

        // Check and update Opening Balance Ledger Entry
        $openingLedger = \App\Models\LedgerEntry::where('ledgerable_id', $customer->id)
            ->where('ledgerable_type', \App\Models\Customer::class)
            ->where('description', 'Opening Balance')
            ->first();

        if (!empty($customer->opening_balance) && $customer->opening_balance > 0) {
            $debit = $customer->opening_balance_type === 'debit' ? $customer->opening_balance : 0;
            $credit = $customer->opening_balance_type === 'credit' ? $customer->opening_balance : 0;

            if ($openingLedger) {
                // Check if values actually changed to avoid unnecessary recalculation
                if ($openingLedger->debit != $debit || $openingLedger->credit != $credit) {
                    $openingLedger->update([
                        'debit' => $debit,
                        'credit' => $credit,
                    ]);
                    $this->recalculateCustomerLedgerBalances($customer->id);
                }
            } else {
                $openingLedger = new \App\Models\LedgerEntry([
                    'transaction_id' => null,
                    'date' => $customer->created_at ?? now(),
                    'description' => 'Opening Balance',
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => 0,
                    'user_id' => auth()->id(),
                ]);
                $openingLedger->ledgerable()->associate($customer);
                $openingLedger->save();

                $this->recalculateCustomerLedgerBalances($customer->id);
            }
        } elseif ($openingLedger) {
            // Opening balance removed
            $openingLedger->delete();
            $this->recalculateCustomerLedgerBalances($customer->id);
        }

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }
    public function search(Request $request)
    {
        $search = $request->get('q', '');
        $perPage = 50; // Adjust as needed

        $query = Customer::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $customers = $query->orderBy('name')->paginate($perPage);

        // Transform the data to match Select2's expected format
        $formattedCustomers = $customers->getCollection()->map(function ($customer) {
            return [
                'id' => $customer->id,
                'text' => $customer->name,
            ];
        });

        return response()->json([
            'results' => $formattedCustomers,
            'pagination' => [
                'more' => $customers->hasMorePages(),
            ],
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        // Block deletion if customer has related data
        $salesCount = $customer->sales()->count();
        $transactionCount = \App\Models\Transaction::where('customer_id', $customer->id)->count();
        $ledgerCount = $customer->ledgerEntries()->count();

        if ($salesCount || $transactionCount || $ledgerCount) {
            $details = collect([
                $salesCount ? "{$salesCount} sale(s)" : null,
                $transactionCount ? "{$transactionCount} transaction(s)" : null,
                $ledgerCount ? "{$ledgerCount} ledger entry/entries" : null,
            ])->filter()->implode(', ');

            return redirect()->route('customers.index')
                ->with('error', "Cannot delete \"{$customer->name}\" — they have {$details}. Remove those records first.");
        }

        // Delete image if exists
        if ($customer->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($customer->image);
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }

    public function exportPdf(Request $request)
    {
        $query = Customer::with('type');
        if ($request->filled('name'))
            $query->where('name', 'like', '%' . $request->name . '%');
        if ($request->filled('type_id'))
            $query->where('type_id', $request->type_id);
        $customers = $query->orderBy('name')->get();

        $pdf = Pdf::loadView('exports.customers', [
            'customers' => $customers,
            'title' => 'Customers Report',
            'filters' => [],
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('customers-report.pdf');
    }

    public function exportCsv(Request $request)
    {
        $query = Customer::with('type');
        if ($request->filled('name'))
            $query->where('name', 'like', '%' . $request->name . '%');
        if ($request->filled('type_id'))
            $query->where('type_id', $request->type_id);
        $customers = $query->orderBy('name')->get();

        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="customers-report.csv"'];
        $callback = function () use ($customers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['#', 'Name', 'Email', 'Phone', 'Type', 'Balance']);
            foreach ($customers as $i => $c) {
                fputcsv($file, [$i + 1, $c->name, $c->email ?? '', $c->phone ?? '', $c->type->name ?? '', $c->balance ?? 0]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    protected function recalculateCustomerLedgerBalances($customerId)
    {
        $entries = \App\Models\LedgerEntry::where('ledgerable_id', $customerId)
            ->where('ledgerable_type', \App\Models\Customer::class)
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $runningBalance = 0;
        foreach ($entries as $entry) {
            $runningBalance += $entry->debit;
            $runningBalance -= $entry->credit;
            if ($entry->balance != $runningBalance) {
                $entry->update(['balance' => $runningBalance]);
            }
        }
    }
}
