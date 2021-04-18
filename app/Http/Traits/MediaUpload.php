<?php
namespace App\http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Image;


trait MediaUpload {

    /**
     * crop an uploaded image
     */
    public function create_image($requestPath, $path, $width, $height){
        $img = Image::make($requestPath)->resize($width, $height, function($constraint){
            $constraint->aspectRatio();
        });
        $img->save($path);
    }

    /**
     * save the image
     *
     * @param [type] $request
     * @return void
     */

    public function save_photo(){
        request()->validate([
            'photos' =>  'required|mimes:jpeg,jpg,png'
        ]);

        try {
         $image = request()->photos;
        $imageFullName = $image->getClientOriginalName();
        $imageName = pathinfo($imageFullName, PATHINFO_FILENAME);
        $imageExt = $image->getClientOriginalExtension();

        $xs = $imageName.'xs'.bin2hex(random_bytes(5)).'.'.$imageExt;
        $sm = $imageName.'sm'.bin2hex(random_bytes(5)).'.'.$imageExt;
        $md = $imageName.'md'.bin2hex(random_bytes(5)).'.'.$imageExt;
        $lg = $imageName.'lg'.bin2hex(random_bytes(5)).'.'.$imageExt;

        $xsmall = public_path('storage/photos/'.$xs);
        $small = public_path('storage/photos/'.$sm);
        $medium = public_path('storage/photos/'.$md);
        $large = public_path('storage/photos/'.$lg);


        $this->create_image(
            $image->getRealPath(), $xsmall, 100, 150
        );

        $this->create_image(
            $image->getRealPath(), $small, 300, 215
        );

        $this->create_image(
            $image->getRealPath(), $medium, 600, 415
        );

        $this->create_image(
            $image->getRealPath(), $large, 1200, 700
        );

        $data = [];
        $data['xsmall'] = url('storage/photos/'.$xs);
        $data['small'] = url('storage/photos/'.$sm);
        $data['medium'] = url('storage/photos/'.$md);
        $data['larger'] = url('storage/photos/'.$lg);

       return $data;

        } catch (Exception $e) {
            //
        }
    }

/**
 * save a file uploaded as a message
 *
 * @param Request $request
 * @return void
 */
    public function files(){
        try {
            $fileFullName = request()->file->getClientOriginalName();
        $fileName = pathinfo($fileFullName, PATHINFO_FILENAME);
        $fileExt = request()->file->getClientOriginalExtension();

        $storedName = $fileName.'file'.bin2hex(random_bytes(5)).'.'.$fileExt;

        $extensions = ['bmp','gif','jpeg','jpg','png', 'webp'];
        $url = url('storage/photos/'.$storedName);
        if(in_array($fileExt, $extensions)){
            $filePath = public_path('storage/photos/'.$storedName);
            $this->create_image(
                request()->file->getRealPath(), $filePath, 400, 300
            );
        }else{
            request()->file('file')->move(public_path("storage/photos"), $storedName);
        }
         return $url;

        } catch (Exception $e) {
            //
        }

    }

}
