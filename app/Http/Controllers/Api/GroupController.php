<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    // GET /api/groups
    public function index()
    {
        // Fetch all groups and eager load the creator relationship
        $groups = Group::with('creator')->latest()->get();
        return GroupResource::collection($groups);
    }

    // POST /api/groups
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $group = Group::create([
            'name'       => $validated['name'],
            'creator_id' => Auth::id(),
        ]);

        // Automatically add the creator as a member of their new group
        $group->members()->attach(Auth::id());

        return new GroupResource($group);
    }
}