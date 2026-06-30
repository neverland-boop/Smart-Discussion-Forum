<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Http\Resources\GroupResource;


class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::with('creator')->get();
        return GroupResource::collection($groups);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:groups,name',
        ]);

        $validated['user_id'] = auth()->id();

        $group = Group::create($validated);

        return new GroupResource($group);
    }

    public function show($id)
    {
        // Fetch the group and its topics in one go
        $group = Group::with('topics')->findOrFail($id);
        
        return new GroupResource($group);
    }
}