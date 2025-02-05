<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\ProfileResource;
use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Controllers\Api\BaseController;

class ProfilesController extends BaseController
{
    /**
     * Display All Profiles.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Profile::query();

        if ($request->query('search')) {
            $searchTerm = $request->query('search');
    
            $query->where(function ($query) use ($searchTerm) {
                $query->where('profile_name', 'like', '%' . $searchTerm . '%');
            });
        }
        $profiles = $query->Orderby("id","desc")->paginate(20);

        return $this->sendResponse(["Profiles"=>ProfileResource::collection($profiles),
        'pagination' => [
            'current_page' => $profiles->currentPage(),
            'last_page' => $profiles->lastPage(),
            'per_page' => $profiles->perPage(),
            'total' => $profiles->total(),
        ]], "Profiles retrieved successfully");
    }

    /**
     * Store Profile.
     * @bodyParam profile_name string The name of the Profile.
     * @bodyParam email string The name of the Profile.
     * @bodyParam active string The name of the Profile.
     * @bodyParam password string The name of the Profile.
     * @bodyParam role string The name of the Profile.
     * @bodyParam mobile string The name of the Profile.
     */
    public function store(StoreProfileRequest $request): JsonResponse
    {
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->active = $request->input('active');
        $user->password = Hash::make($request->input('password'));
        $user->save();
        
        // $memberRole = $request->input("role");
        $memberRole = $request->input("role");
        $memberRole = Role::where("name",$memberRole)->first();
       
        $user->assignRole($memberRole);
        
        $profile = new Profile();
        $profile->user_id = $user->id;
        $profile->profile_name = $request->input('name');
        $profile->email = $request->input('email');
        $profile->mobile = $request->input('mobile');
        $profile->save();
       
        return $this->sendResponse(['User'=> new UserResource($user), 'Profile'=>new ProfileResource($profile)], "Profile stored successfully");
    }

    /**
     * Show Profile.
     */
    public function show(string $id): JsonResponse
    {
        $profile = Profile::find($id);

        if(!$profile){
            return $this->sendError("Profile not found", ['error'=>'Profile not found']);
        }
        $user = User::find($profile->user_id);
        return $this->sendResponse(['User'=> new UserResource($user), 'Profile'=>new ProfileResource($profile)], "Profile retrived successfully");
    }

    /**
     * Update Profile.
     * @bodyParam profile_name string The name of the Profile.
     * @bodyParam email string The name of the Profile.
     * @bodyParam active string The name of the Profile.
     * @bodyParam password string The name of the Profile.
     * @bodyParam role string The name of the Profile.
     * @bodyParam mobile string The name of the Profile.
     */
    public function update(UpdateProfileRequest $request, string $id): JsonResponse
    {
        $profile = Profile::find($id);

        if(!$profile){
            return $this->sendError("Profile not found", ['error'=>'Profile not found']);
        }
        

        $user = User::find($profile->user_id);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->active = $request->input('active');
        if($request->input('password'))
       {
        $user->password = Hash::make($request->input('password'));
       }
        $user->save();

        $memberRole = $request->input("role");
        // $memberRole = Role::where("name",$memberRole)->first();
        // $user->assignRole($memberRole);
         if ($memberRole) {
        // Remove existing roles before assigning a new one
        $user->syncRoles(); // This removes all roles and assigns the new one

        // Assign new role
        $user->assignRole($memberRole);
    }
        

        $profile->profile_name = $request->input('name');
        $profile->email = $request->input('email');
        $profile->mobile = $request->input('mobile');
        $profile->save();
       
        return $this->sendResponse(['User'=> new UserResource($user), 'Profile'=>new ProfileResource($profile)], "Profile updated successfully");

    }

    /**
     * Remove Employee.
     */
    public function destroy(string $id): JsonResponse
    {
        $profile = Profile::find($id);
        if(!$profile){
            return $this->sendError("profile not found", ['error'=> 'profile not found']);
        }
        $user = User::find($profile->user_id);
        $profile->delete();
        $user->delete();
        return $this->sendResponse([], "profile deleted successfully");
    }

}