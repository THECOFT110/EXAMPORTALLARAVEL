<?php

namespace App\Http\Controllers;

use App\Models\College;
use Illuminate\Http\Request;

class CollegeController extends Controller
{
    /**
     * Get all colleges
     */
    public function index(Request $request)
    {
        $query = College::query();

        if ($request->filled('active_only')) {
            $query->where('is_active', true);
        }

        if ($request->filled('type')) {
            $query->where('type', strtoupper($request->type));
        }

        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('code', 'like', '%'.$search.'%')
                    ->orWhere('city', 'like', '%'.$search.'%');
            });
        }

        $colleges = $query->orderBy('name')->get();

        return response()->json($colleges);
    }

    /**
     * Get single college
     */
    public function show(Request $request, string $id)
    {
        $college = College::findOrFail($id);

        return response()->json($college);
    }

    /**
     * Create college (admin only)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:colleges,code',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'principal_name' => 'nullable|string',
            'type' => 'required|in:BOYS,GIRLS,COED',
            'boys_capacity' => 'required|integer|min:0',
            'girls_capacity' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $college = College::create($validated);

        return response()->json([
            'message' => 'College created successfully.',
            'college' => $college,
        ], 201);
    }

    /**
     * Update college (admin only)
     */
    public function update(Request $request, string $id)
    {
        $college = College::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:50|unique:colleges,code,'.$id,
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'principal_name' => 'nullable|string',
            'type' => 'sometimes|in:BOYS,GIRLS,COED',
            'boys_capacity' => 'sometimes|integer|min:0',
            'girls_capacity' => 'sometimes|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $college->update($validated);

        return response()->json([
            'message' => 'College updated successfully.',
            'college' => $college,
        ]);
    }

    /**
     * Delete college (admin only)
     */
    public function destroy(Request $request, string $id)
    {
        $college = College::findOrFail($id);

        // Check if college has enrollments
        if ($college->enrollments()->exists()) {
            return response()->json([
                'message' => 'Cannot delete college with existing enrollments.',
            ], 400);
        }

        $college->delete();

        return response()->json([
            'message' => 'College deleted successfully.',
        ]);
    }

    /**
     * Get college statistics
     */
    public function statistics(Request $request, string $id)
    {
        $college = College::with(['enrollments' => function ($query) {
            $query->where('status', 'APPROVED');
        }])->findOrFail($id);

        $maleCount = $college->enrollments->where('gender', 'MALE')->count();
        $femaleCount = $college->enrollments->where('gender', 'FEMALE')->count();

        return response()->json([
            'college' => [
                'id' => $college->id,
                'name' => $college->name,
                'code' => $college->code,
                'type' => $college->type,
            ],
            'capacity' => [
                'boys' => $college->boys_capacity,
                'girls' => $college->girls_capacity,
                'total' => $college->boys_capacity + $college->girls_capacity,
            ],
            'allocated' => [
                'boys' => $maleCount,
                'girls' => $femaleCount,
                'total' => $maleCount + $femaleCount,
            ],
            'available' => [
                'boys' => max(0, $college->boys_capacity - $maleCount),
                'girls' => max(0, $college->girls_capacity - $femaleCount),
            ],
        ]);
    }
}
