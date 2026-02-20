<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Flavour;
use App\Models\Packing;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Apply filters based on request
        if ($request->has('flavour_id') && $request->flavour_id != '') {
            $query->where('flavour_id', $request->flavour_id);
        }

        if ($request->has('packing_id') && $request->packing_id != '') {
            $query->where('packing_id', $request->packing_id);
        }

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('name') && $request->name != '') {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $products = $query->paginate(20);

        $flavours = Flavour::all();
        $packings = Packing::all();
        $categories = Category::all();

        return view('products.index', compact('products', 'flavours', 'packings', 'categories', 'request'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $flavours = Flavour::all();
        $packings = Packing::all();
        $categories = Category::all();
        return view('products.create', compact('flavours', 'packings', 'categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request data, including the image
        $request->validate([
            'name'          => 'required|string|max:255|unique:products,name',
            'sku'           => 'nullable|string|unique:products,sku',
            'flavour_id'    => 'nullable|exists:flavours,id',
            'packing_id'    => 'nullable|exists:packings,id',
            'category_id'   => 'nullable|exists:categories,id',
            'barcode'       => 'nullable|string|unique:products,barcode',
            'weight'        => 'nullable|numeric',
            'volume'        => 'nullable|numeric',
            'status'        => 'required|in:active,inactive,discontinued',
            'gst'           => 'required|numeric|min:0|max:100',
            'reorder_level'  => 'required|integer|min:0',
            'max_stock_level'=> 'required|integer|min:0',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Retrieve all input data
        $data = $request->all();

        // Handle image upload if present
        if ($request->hasFile('image')) {
            // Generate a slug from the product name
            $slug = Str::slug($request->name);

            // Get the uploaded image
            $image = $request->file('image');

            // Get original image extension
            $extension = $image->getClientOriginalExtension();

            // Create initial image name using slug
            $imageName = $slug . '.' . $extension;

            // Ensure the image name is unique to prevent overwriting
            $i = 1;
            while (Storage::disk('public')->exists('products/' . $imageName)) {
                $imageName = $slug . '-' . $i . '.' . $extension;
                $i++;
            }

            // Store the image in the 'public/images/products' directory
            $image->storeAs('products', $imageName, 'public');

            // Save the image path in the data array
            $data['image'] = 'products/' . $imageName;
        } else {
            // Set default image path if no image was uploaded
            $data['image'] = 'products/product.png';
        }

        // Create the product with the validated data
        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $product->load(['flavour', 'packing', 'category']);
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $flavours = Flavour::all();
        $packings = Packing::all();
        $categories = Category::all();
        return view('products.edit', compact('product', 'flavours', 'packings', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        // Validate incoming request data, including the image
        $request->validate([
            'name'          => 'required|string|max:255',
            'sku'           => 'required|string|unique:products,sku,' . $product->id,
            'flavour_id'    => 'required|exists:flavours,id',
            'packing_id'    => 'required|exists:packings,id',
            'category_id'   => 'nullable|exists:categories,id',
            'barcode'       => 'nullable|string|unique:products,barcode,' . $product->id,
            'weight'        => 'nullable|numeric',
            'volume'        => 'nullable|numeric',
            'status'        => 'required|in:active,inactive,discontinued',
            'gst'           => 'required|numeric|min:0|max:100',
            'reorder_level'  => 'required|integer|min:0',
            'max_stock_level'=> 'required|integer|min:0',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Image validation
        ]);

        // Retrieve all input data
        $data = $request->all();

        $originalName = $product->name;
        $newName = $request->name;

        // Check if the product name has changed
        if ($originalName !== $newName) {
            // Generate a new slug from the updated name
            $slug = Str::slug($newName);

            if ($product->image) {
                $oldImagePath = $product->image;

                // Extract the current image extension
                $extension = pathinfo($oldImagePath, PATHINFO_EXTENSION);

                // Create the new image name using the new slug
                $newImageName = $slug . '.' . $extension;

                // Ensure the new image name is unique
                $i = 1;
                while (Storage::disk('public')->exists('images/products/' . $newImageName)) {
                    $newImageName = $slug . '-' . $i . '.' . $extension;
                    $i++;
                }

                // Rename the image in storage
                Storage::disk('public')->move($oldImagePath, 'images/products/' . $newImageName);

                // Update the image path in the data array
                $data['image'] = 'images/products/' . $newImageName;
            }
        }

        // Handle new image upload if present
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            // Generate a slug from the product name
            $slug = Str::slug($request->name);

            // Get the uploaded image
            $image = $request->file('image');

            // Get original image extension
            $extension = $image->getClientOriginalExtension();

            // Create initial image name using slug
            $imageName = $slug . '.' . $extension;

            // Ensure the image name is unique to prevent overwriting
            $i = 1;
            while (Storage::disk('public')->exists('images/products/' . $imageName)) {
                $imageName = $slug . '-' . $i . '.' . $extension;
                $i++;
            }

            // Store the new image in the 'public/images/products' directory
            $image->storeAs('images/products', $imageName, 'public');

            // Save the new image path in the data array
            $data['image'] = 'images/products/' . $imageName;
        }

        // Update the product with the validated data
        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function search(Request $request)
    {
        $search = $request->get('q', '');
        $page = $request->get('page', 1);
        $perPage = 50; // Adjust as needed

        $query = Product::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
        }

        // Select the necessary fields, including 'image'
        $products = $query->orderBy('name')
                          ->paginate($perPage, ['id', 'name', 'sku', 'image'], 'page', $page);

        // Transform the data to match Select2's expected format
        $formattedProducts = $products->getCollection()->map(function ($product) {
            return [
                'id' => $product->id,
                'text' => "{$product->name} (SKU: {$product->sku})", // Combining name and SKU for better display
                'image_url' => $product->image ? url($product->image) : asset('products/product.png'),
                // 'Storage::url($product->image)' generates the correct URL to access the image
                // If 'image' is null, a default image is provided
            ];
        });

        return response()->json([
            'results' => $formattedProducts,
            'pagination' => [
                'more' => $products->hasMorePages(),
            ],
        ]);
    }



    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        // Delete the associated image if it exists
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        // Delete the product from the database
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
    public function getBatches(Request $request,$productId)
    {
        $product = Product::with('batches')->findOrFail($productId);

        $batches = $product->batches->map(function ($batch) {
            return [
                'batch_no' => $batch->batch_no,
                'purchase_date' => $batch->purchase_date,
                'expiry_date' => $batch->expiry_date,
                'invoice_no' => $batch->invoice_no,
            ];
        });

        return response()->json(['success' => true,'batches' => $batches]);
    }
}
