<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageController extends Controller
{
    //
    public function upload(Request $request) {
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name = $image->getClientOriginalName();//time().'.'.$image->getClientOriginalExtension();
            $liter = substr($name,0,1);
            $destinationPath = public_path('/images/' . $liter);
            $flag = !file_exists($destinationPath . '/' . $name);
            if (!$flag) {
                $flag = $image->getSize() != filesize($destinationPath . '/' . $name);
                if ($flag) {
                    $name = time() . $name;
                }
            }
            if ($flag) {
                $image->move($destinationPath, $destinationPath . '/' . $name);
            }
            return response()->json(['url' => '/images/' . $liter . '/' . $name ]);

            //return back()->with('success','Image Upload successfully');
        }
    }
}
