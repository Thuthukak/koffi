<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServicesController extends Controller
{
    /**
     * Display a listing of services
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(Service::all());
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
            'duration' => ['required', 'integer', 'min:1'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'] // 5MB max
        ]);

        $photoPath = null;
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photoPath = $this->handlePhotoUpload($request->file('photo'));
        }

        $service = Service::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'duration' => $request->duration,
            'photo' => $photoPath
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
            'duration' => ['required', 'integer', 'min:1'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'] // 5MB max
        ]);

        $photoPath = $service->photo; // Keep existing photo by default
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if it exists
            if ($service->photo) {
                $this->deletePhoto($service->photo);
            }
            
            $photoPath = $this->handlePhotoUpload($request->file('photo'));
        }
        
        $service->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'duration' => $request->duration,
            'photo' => $photoPath
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
        
        // Delete associated photo if it exists
        if ($service->photo) {
            $this->deletePhoto($service->photo);
        }
        
        $service->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully'
        ]);
    }

    /**
     * Handle photo upload and return the storage path
     */
    private function handlePhotoUpload($photo)
    {
        // Generate a unique filename
        $filename = Str::uuid() . '.' . $photo->getClientOriginalExtension();
        
        // Store the photo in the public disk under 'services' directory
        $path = $photo->storeAs('services', $filename, 'public');
        
        // Return the full URL to the photo
        return Storage::url($path);
    }

    /**
     * Delete a photo from storage
     */
    private function deletePhoto($photoPath)
    {
        // Extract the relative path from the full URL
        $relativePath = str_replace('/storage/', '', $photoPath);
        
        // Delete the file if it exists
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }

    /**
     * Remove photo from a service
     */
    public function removePhoto($id)
    {
        $service = Service::findOrFail($id);
        
        if ($service->photo) {
            $this->deletePhoto($service->photo);
            $service->update(['photo' => null]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Photo removed successfully',
            'service' => $service
        ]);
    }

}