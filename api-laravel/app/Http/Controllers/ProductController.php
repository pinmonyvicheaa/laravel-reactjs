<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // Construct method for middleware checks (if needed)
    // public function __construct()
    // {
    //     $this->middleware('auth:sanctum');
    //     $this->middleware('role:admin')->except(['index', 'show']);
    // }

    public function index()
    {
        return response()->json(Product::with('category')->get(), 200);
    }

    public function store(Request $request)
    {
        if (!Gate::allows('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'image_url' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
                'category_id' => 'required|exists:categories,id',
            ]);

            // Handle image upload if present
            if ($request->hasFile('image_url')) {
                $imagePath = $request->file('image_url')->store('images', 'public');
                $imageUrl = asset('storage/' . $imagePath);
            } else {
                $imageUrl = null;
            }

            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock,
                'category_id' => $request->category_id,
                'image_url' => $imageUrl,
            ]);

            return response()->json($product->load('category'), 201);
        } catch (\Exception $e) {
            Log::error('Error creating product: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => $e->getMessage(), 'stack' => $e->getTraceAsString()], 500);
        }
    }

    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);
        return response()->json($product, 200);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'sometimes|required|exists:categories,id',
        ]);

        // Handle image update
        if ($request->hasFile('image_url')) {
            // Delete old image if exists
            if ($product->image_url) {
                $oldImagePath = str_replace(asset('storage/'), '', $product->image_url);
                Storage::disk('public')->delete($oldImagePath);
            }

            // Store new image
            $imagePath = $request->file('image_url')->store('images', 'public');
            $product->image_url = asset('storage/' . $imagePath);
        }

        // Update product data (excluding image_url since we handled it manually)
        $product->update($request->except('image_url'));

        return response()->json($product->load('category'), 200);
    }

    public function destroy($id)
    {
        // Step 1: Find the product by ID or fail with an error
        $product = Product::findOrFail($id);

        // Step 2: Check if the user is an admin
        if (!Gate::allows('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Step 3: Handle image deletion (if the product has an image)
        if ($product->image_url) {
            try {
                // Extract the relative image path from the full URL
                $imagePath = str_replace(asset('storage/'), '', $product->image_url);

                // Check if the image file exists in the storage
                if (Storage::disk('public')->exists($imagePath)) {
                    // Delete the image file from the public storage
                    Storage::disk('public')->delete($imagePath);
                    Log::info("Image deleted: $imagePath");
                } else {
                    Log::warning("Image not found for deletion: $imagePath");
                }
            } catch (\Exception $e) {
                // Log error if there's any issue with image deletion
                Log::error("Error deleting image: " . $e->getMessage());
                return response()->json(['message' => 'Error deleting image'], 500);
            }
        }

        // Step 4: Attempt to delete the product permanently
        try {
            // Permanently delete the product from the database
            $product->forceDelete();

            // Log success
            Log::info("Product deleted successfully: " . $product->id);
        } catch (\Exception $e) {
            // Log error if there's any issue with product deletion
            Log::error("Error deleting product: " . $e->getMessage());
            return response()->json(['message' => 'Error deleting product'], 500);
        }

        // Step 5: Return a success response if everything worked
        return response()->json(['message' => 'Product deleted successfully'], 200);
    }

    public function restore($id)
    {
        // Restore soft-deleted product (if soft deletes were enabled in your model)
        $product = Product::withTrashed()->findOrFail($id);
        if (!Gate::allows('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $product->restore();
        return response()->json(['message' => 'Product restored successfully'], 200);
    }
}
