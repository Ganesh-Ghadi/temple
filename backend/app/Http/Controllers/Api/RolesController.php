<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use Spatie\Permission\Models\Permission;
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

    public function show(string $id): JsonResponse
    {    
        $role = Role::find($id);
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $permissions = Permission::get();
        
        return $this->sendResponse(['Role'=>new RoleResource($role), 'RolePermissions'=>$rolePermissions, 'Permissions'=>$permissions], "Permissions generated successfully");
    }   
       
    public function update(Role $role, Request $request)
    {
        $request->validate([
            'name' => 'required',
            'guard_name' => 'required',
        ]);    
        $input = $request->all();
        $role->update($input);
        $data = $role->syncPermissions($request->permission);
        $request->session()->flash('success', 'Role updated successfully!');
        return redirect()->route('roles.index');       
    }

}