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
}