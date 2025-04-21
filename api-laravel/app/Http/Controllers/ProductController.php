<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller; // Ensure this is imported
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    // public function __construct()
    // {
    //     // Admin can create, update, delete. Customer can only get products.
    //     $this->middleware('auth:sanctum'); // Require authentication
    //     $this->middleware('role:admin')->except(['index', 'show']);
    // }

    public function index()
    {
        return response()->json(Product::with('category')->get(), 200);
    }

    public function store(Request $request)
    {
           // Check if the user is an admin
           if (!Gate::allows('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'image_url' => 'nullable|image|mimes:jpg,png,jpeg|max:2048', // Validate image
                'category_id' => 'required|exists:categories,id',
            ]);

            if ($request->hasFile('image_url')) {
                $imagePath = $request->file('image_url')->store('images', 'public'); // Store image in /storage/app/public/images
                $imageUrl = asset('storage/' . $imagePath); // Generate accessible URL
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

            //$product = Product::create($request->all()); 
            return response()->json($product->load('category'), 201);
            
        } catch (\Exception $e) {
            // Log the full exception message and stack trace
            Log::error('Error creating product: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString(),
            ]);

            // Return more detailed error message temporarily to help with debugging
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
    
        // Validate input fields
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
        // $category = Product::findOrFail($id);
        // $category->delete(); // Soft delete the category
        // return response()->json(['message' => 'Category archived successfully'], 200);

        $product = Product::findOrFail($id);
        if (!Gate::allows('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        if ($product->image_url) {
            Storage::disk('public')->delete(str_replace(asset('storage/'), '', $product->image_url));
        }

        $product->delete();
        return response()->json(['message' => 'Product deleted successfully'], 200);
    }


    public function restore($id)
    {
        // $product = Product::withTrashed()->findOrFail($id);
        // $product->restore(); // Restore the soft-deleted product
        // return response()->json(['message' => 'Product restored successfully'], 200);

        $product = Product::withTrashed()->findOrFail($id);
        if (!Gate::allows('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $product->restore();
        return response()->json(['message' => 'Product restored successfully'], 200);
    }
}
