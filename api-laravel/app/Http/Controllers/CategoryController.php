<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Category::all(), 200);
    }

    /**
     * Store a newly created resource in storage (Admin Only).
     */
    public function store(Request $request)
    {
        // Ensure the user is authenticated and has admin role
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $category = Category::create($request->all());
        return response()->json($category, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category, 200);
    }

    /**
     * Update the specified resource in storage (Admin Only).
     */
    public function update(Request $request, string $id)
    {
        // Ensure the user is authenticated and has admin role
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $category = Category::findOrFail($id);
        $category->update($request->all());

        return response()->json($category, 200);
    }

    /**
     * Remove the specified resource from storage (Soft Delete - Admin Only).
     */
    public function destroy(string $id)
    {
        // Ensure the user is authenticated and has admin role
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $category = Category::findOrFail($id);
        $category->delete(); // Soft delete
        return response()->json(['message' => 'Category archived successfully'], 200);
    }
}
