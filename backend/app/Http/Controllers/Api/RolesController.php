<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Http\Controllers\Api\BaseController;

    /**
     * @group Roles Management
     */

class RolesController extends BaseController
{
    /**
     * All Roles.
     */
    public function index(Request $request):JsonResponse
    {
        $query = Role::query();

        if ($request->query('search')) {
            $searchTerm = $request->query('search');
    
            $query->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%');
            });
        }
        $roles = $query->orderBy("id", "DESC")->paginate(15);

        return $this->sendResponse(["Roles"=>RoleResource::collection($roles),
        'Pagination' => [
            'current_page' => $roles->currentPage(),
            'last_page' => $roles->lastPage(),
            'per_page' => $roles->perPage(),
            'total' => $roles->total(),
        ]], "Roles retrived successfully");
    }

}
