<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function imageupload(Request $request) {

        $request->validate([
            'file' => 'required|image|mimes:jpeg,jpg,png'
        ]);
        
        $ext = $request->file->extension();
        $imageName = time().'.'.$ext;

        //$imageName = time().'.'.$request->file->extension();
        $request->file->move(public_path('media/images'), $imageName);

        return [
            'location' => asset('media/images/'.$imageName)
        ];
    }
}
