<?php

namespace App\Http\Controllers\Api\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DealerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dealer = Dealer::get();
        return $dealer;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $fields = $request->validate([
            'name' => 'required|max:255',
            'state' => 'required|max:255',
            'email' => 'required|max:255',
            'phone' => 'required|max:255',
            'address' => 'required|max:255',
            'city' => 'required|max:255',
            'zip' => 'required|max:255',
            'password' => 'required|max:255',
        ]);

        $post = Dealer::create([
            'name' => $request->name,
            'state' => $request->state,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'zip' => $request->zip,
            'password' => bcrypt($request->password),
        ]);

        return ['post' => $post];
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $dealer = Dealer::find($id);
        return $dealer;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Debugging: Check raw input before Laravel parsing
        logger('Raw input:', [file_get_contents('php://input')]);

        // Debugging: Check headers
        logger('Headers:', $request->headers->all());

        // Check JSON
        if (!$request->isJson()) {
            return response()->json([
                'error' => 'Content-Type must be application/json',
                'received_headers' => $request->headers->all()
            ], 415);
        }

        // Proceed with update...
        $dealer = Dealer::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:dealers,email,'.$id,
            'phone' => 'sometimes|string|max:20',
            'password' => 'sometimes|string|min:8',
        ]);

        $dealer->update($validated);

        return response()->json([
            'success' => true,
            'data' => $dealer
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $dealer = Dealer::findOrFail($id);
        $dealer->delete();

        return response()->json('Dealer deleted sucessfully');
    }
}
