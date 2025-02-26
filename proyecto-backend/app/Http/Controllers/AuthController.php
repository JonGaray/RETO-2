<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $credentials = $request->only('email', 'password');
        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'error' => 'Correo o contraseña errónea.',
            ], 401);
        }
        $user = Auth::user();
        return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => $user,
        ]);
    }
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }
    public function updateStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->status = $request->status;
        $user->save();

        return response()->json(['message' => 'Estado actualizado correctamente']);
    }
    public function create(Request $request) {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'username1' => 'required|string|max:255',
            'username2' => 'required|string|max:255',
            'role' => 'required|in:user,tecnico,admin',
        ]);

        try {
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => bcrypt($validatedData['password']),
                'username1' => $validatedData['username1'],
                'username2' => $validatedData['username2'],
                'role' => $validatedData['role'],
            ]);

            return response()->json([
                'message' => 'Usuario creado con éxito',
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function save(Request $request, $id){
    try {
        // Validar los datos de entrada
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'username1' => 'required|string|max:255|unique:users,username1,' . $id,
            'username2' => 'required|string|max:255|unique:users,username2,' . $id,
            'role' => 'required|in:user,tecnico,admin',
        ]);
        $user = User::findOrFail($id);
        $user->update($validatedData);
        return response()->json([
            'message' => 'Usuario actualizado correctamente.',
            'user' => $user,
        ], 200);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => 'Error en la validación.',
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error al actualizar el usuario.',
            'error' => $e->getMessage(),
        ], 500);
    }
    }
    public function searchByName(Request $request){
        $query = $request->input('query');
        $users = User::where('name', 'like', "%{$query}%")->get();
        return response()->json($users);
    }
}
