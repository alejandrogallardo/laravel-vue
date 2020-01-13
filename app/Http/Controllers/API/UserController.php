<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// add user
use App\User;
use Illuminate\Support\Facades\Hash;
// use Intervention\Image\ImageManager;
use Intervention\Image\Facades\Image as Image; 
//use Intervention\Image\Exception\NotReadableException;

class UserController extends Controller
{
    /**
     * Proteccion de rutas
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        // $this->authorize('isAdmin'); // Esto limita todo el controlador
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return User::all();
        if ( \Gate::allows( 'isAdmin' ) || \Gate::allows( 'isAuthor' ) ) {
            return User::latest()->paginate(5);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validations
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users',
            'password' => 'required|string|min:6'
        ]);

        // return ['message' => 'I have your data'];
        // return $request->all();
        return User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'type' => $request['type'],
            'bio' => $request['bio'],
            'photo' => $request['photo'],
            'password' => Hash::make($request['type']),
        ]);
    }

    /**
     * 
     */
    public function updateProfile(Request $request)
    {
        $user = auth('api')->user();
        // return $request->photo;

        // Validations
        $this->validate($request,[
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,'.$user->id,
            'password' => 'sometimes|required|min:6'
        ]);

        $currentPhoto = $user->photo;

        if( $request->photo != $currentPhoto ){
            $name = time().'.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
            Image::make($request->photo)->save(public_path('img/profile/').$name);

            $request->merge(['photo' => $name ]);

            $userPhoto = public_path('img/profile/').$currentPhoto;
            if(file_exists($userPhoto)){
                @unlink($userPhoto);
            }

        }
        
        if(!empty($request->password)){
            $request->merge(['password' => Hash::make($request['password'])]);
        } 

        $user->update($request->all());
        return ['message' => 'Success'];

    }
    
    /**
     * 
     */
    public function profile()
    {
        return auth('api')->user();
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        // Validations
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,'.$user->id,
            'password' => 'sometimes|string|min:6'
        ]);
        $user->update($request->all());
        return ['message' => 'User updated'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('isAdmin');
        $user = User::findOrFail($id);

        // Delete the user

        $user->delete();
        return ['message' => 'User deleted'];
    }
    
    /**
     * 
     */
    public function search(){
        if ($search = \Request::get('q')) {
            $users = User::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%")
                        ->orWhere('email','LIKE',"%$search%");
            })->paginate(20);
        }else{
            $users = User::latest()->paginate(5);
        }
        return $users;
    }
}
