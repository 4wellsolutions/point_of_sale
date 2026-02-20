<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class VendorController extends Controller
{
    /**
     * Display a listing of the Vendors with filters.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Retrieve all types for the filter dropdown
        $types = Type::orderBy('name')->get();

        // Initialize the query
        $query = Vendor::query();

        // Apply filters based on the request inputs

        // Filter by Name (partial match)
        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        // Filter by Email (partial match)
        if ($request->filled('email')) {
            $query->where('email', 'LIKE', '%' . $request->email . '%');
        }

        // Filter by Phone (partial match)
        if ($request->filled('phone')) {
            $query->where('phone', 'LIKE', '%' . $request->phone . '%');
        }

        // Filter by WhatsApp (partial match)
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
        $vendors = $query->orderBy('created_at', 'desc')->paginate(50)->withQueryString();

        return view('vendors.index', compact('vendors', 'types'));
    }

    /**
     * Show the form for creating a new Vendor.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $types = Type::orderBy('name')->get();
        return view('vendors.create', compact('types'));
    }

    /**
     * Store a newly created Vendor in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validation rules
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:vendors,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'type_id' => 'nullable|exists:types,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            // Generate a slug for the vendor name
            $slug = Str::slug($request->name);

            // Get the extension of the uploaded image
            $extension = $request->file('image')->getClientOriginalExtension();

            // Generate a unique image name
            $imageName = $slug . '.' . $extension;

            // Check if the image already exists to prevent overwriting
            if (Storage::disk('public')->exists('vendors/' . $imageName)) {
                $imageName = $slug . '-' . time() . '.' . $extension;
            }

            // Store the image in the 'vendors' directory within the 'public' disk
            $data['image'] = $request->file('image')->storeAs('vendors', $imageName, 'public');
        }

        Vendor::create($data);

        return redirect()->route('vendors.index')->with('success', 'Vendor created successfully.');
    }

    /**
     * Show the form for editing the specified Vendor.
     *
     * @param \App\Models\Vendor $vendor
     * @return \Illuminate\View\View
     */
    public function edit(Vendor $vendor)
    {
        $types = Type::orderBy('name')->get();
        return view('vendors.edit', compact('vendor', 'types'));
    }

    /**
     * Update the specified Vendor in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Vendor $vendor
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Vendor $vendor)
    {
        // Validation rules
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:vendors,email,' . $vendor->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'type_id' => 'nullable|exists:types,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        // Check if the name has changed and an image exists
        if ($request->name !== $vendor->name && $vendor->image) {
            // Generate slug for the new name
            $newSlug = Str::slug($request->name);

            // Get the extension of the existing image
            $extension = pathinfo($vendor->image, PATHINFO_EXTENSION);

            // Generate new image filename
            $newImageName = $newSlug . '.' . $extension;

            // Define paths
            $oldImagePath = $vendor->image; // e.g., 'vendors/jane-doe.jpg'
            $newImagePath = 'vendors/' . $newImageName;

            // Check if the new image name already exists to avoid overwriting
            if (Storage::disk('public')->exists($newImagePath)) {
                return redirect()->back()->with('error', 'A vendor image with the new name already exists. Please choose a different name.');
            }

            // Rename the image file
            Storage::disk('public')->move($oldImagePath, $newImagePath);

            // Update the image path in data
            $data['image'] = $newImagePath;
        }

        // Handle new image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists and not already renamed
            if ($vendor->image && !($request->name !== $vendor->name && $vendor->image)) {
                Storage::disk('public')->delete($vendor->image);
            }

            // Generate slug for the vendor name
            $slug = Str::slug($request->name);

            // Get the extension of the uploaded image
            $extension = $request->file('image')->getClientOriginalExtension();

            // Generate a unique image name
            $newImageName = $slug . '.' . $extension;

            // Check if the image already exists to prevent overwriting
            if (Storage::disk('public')->exists('vendors/' . $newImageName)) {
                $newImageName = $slug . '-' . time() . '.' . $extension;
            }

            // Store the new image
            $newImagePath = $request->file('image')->storeAs('vendors', $newImageName, 'public');

            $data['image'] = $newImagePath;
        }

        // Update the vendor
        $vendor->update($data);

        return redirect()->route('vendors.index')->with('success', 'Vendor updated successfully.');
    }

    public function search(Request $request)
    {
        $search = $request->get('q', '');
        $perPage = 50; // Adjust as needed

        $query = Vendor::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $vendors = $query->orderBy('name')->paginate($perPage);

        // Transform the data to match Select2's expected format
        $formattedVendors = $vendors->getCollection()->map(function ($vendor) {
            return [
                'id' => $vendor->id,
                'text' => $vendor->name,
            ];
        });

        return response()->json([
            'results' => $formattedVendors,
            'pagination' => [
                'more' => $vendors->hasMorePages(),
            ],
        ]);
    }


    /**
     * Remove the specified Vendor from storage.
     *
     * @param \App\Models\Vendor $vendor
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Vendor $vendor)
    {
        // Delete image if exists
        if ($vendor->image) {
            Storage::disk('public')->delete($vendor->image);
        }

        $vendor->delete();

        return redirect()->route('vendors.index')->with('success', 'Vendor deleted successfully.');
    }

    public function exportPdf(Request $request)
    {
        $query = Vendor::with('type');
        if ($request->filled('name'))
            $query->where('name', 'like', '%' . $request->name . '%');
        if ($request->filled('type_id'))
            $query->where('type_id', $request->type_id);
        $vendors = $query->orderBy('name')->get();

        $pdf = Pdf::loadView('exports.vendors', [
            'vendors' => $vendors,
            'title' => 'Vendors Report',
            'filters' => [],
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('vendors-report.pdf');
    }

    public function exportCsv(Request $request)
    {
        $query = Vendor::with('type');
        if ($request->filled('name'))
            $query->where('name', 'like', '%' . $request->name . '%');
        if ($request->filled('type_id'))
            $query->where('type_id', $request->type_id);
        $vendors = $query->orderBy('name')->get();

        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="vendors-report.csv"'];
        $callback = function () use ($vendors) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['#', 'Name', 'Email', 'Phone', 'Type', 'Balance']);
            foreach ($vendors as $i => $v) {
                fputcsv($file, [$i + 1, $v->name, $v->email ?? '', $v->phone ?? '', $v->type->name ?? '', $v->balance ?? 0]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
