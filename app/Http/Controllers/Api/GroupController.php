<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GroupService;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index(Request $request, GroupService $groupService)
    {
        return response()->json([
            'my_groups' => $groupService->getUserGroups($request->user()),
            'available_groups' => $groupService->getAvailableGroups($request->user())
        ]);
    }

    public function join(Request $request, GroupService $groupService)
    {
        $request->validate(['group_id' => 'required|exists:groups,id']);
        $result = $groupService->joinGroup($request->group_id, $request->user());
        return response()->json($result);
    }

    public function store(Request $request, GroupService $groupService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'topic_title' => 'required|string|max:255',
            'topic_description' => 'required|string|max:255',
        ]);

        try {
            $group = $groupService->createGroupWithTopic($validated, $request->user());
            
            return response()->json([
                'success' => true, 
                'message' => 'Group created successfully',
                'data' => $group
            ], 201);
            
        } catch (\Exception $e) {
            // Log it for your own debugging
            logger($e->getMessage());
            return response()->json(['error' => 'Failed to create group.'], 500);
        }
    }
}