<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Route;

class ServicesController extends Controller
{
    /**
     * Display a listing of services
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return Service::all()->map(function($service) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'duration' => $service->duration,
                'price' => number_format($service->price, 2),
                'description' => $service->description
            ];
        });
    }

    /**
     * Store a newly created service
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => ['required', 'numeric', 'min:0'],
            'duration' => ['required', 'integer', 'min:1']
        ]);
    
        $service = Service::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'duration' => $request->duration
        ]);
    
        return response()->json([
            'success' => true,
            'message' => 'Service created successfully',
            'service' => $service
        ]);
    }
    
    /**
     * Display the specified service
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $service = Service::findOrFail($id);
        return response()->json($service);
    }

    /**
     * Update the specified service
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => ['required', 'numeric', 'min:0'],
            'duration' => ['required', 'integer', 'min:1']
        ]);
        
        $service->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'duration' => $request->duration
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully',
            'service' => $service
        ]);
    }

    /**
     * Remove the specified service
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully'
        ]);
    }
}