<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Chapter;

class SecurePdfController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function view(Request $request)
    {
        $path = $request->query('path');
        
        // Remove the /storage/ prefix if present
        if (strpos($path, '/storage/') === 0) {
            $path = substr($path, 9); // Remove the first 9 characters (/storage/)
        }
        
        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404, 'PDF not found');
        }
        
        // Extract chapter ID from the path (assuming format like 'chapter-notes/file.pdf')
        $pathParts = explode('/', $path);
        if (count($pathParts) >= 1 && $pathParts[0] === 'chapter-notes') {
            // Try to find a chapter that uses this PDF file
            $chapter = Chapter::where('pdf_notes', $path)->first();
            
            if ($chapter) {
                $user = Auth::user();
                
                // Check if user is admin or enrolled in the course
                if (!$user->isAdmin() && !$user->enrolledCourses->contains($chapter->module->course)) {
                    abort(403, 'You do not have access to this PDF');
                }
            }
        }
        
        $file = Storage::disk('public')->path($path);
        
        // Set headers to prevent download and caching
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="document.pdf"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-Download-Options' => 'noopen'
        ];
        
        // Stream the file with headers
        return new StreamedResponse(function () use ($file) {
            readfile($file);
        }, 200, $headers);
    }
}
