<?php

namespace App\http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Image;


trait MediaUpload
{

    /**
     * crop an uploaded image
     */
    public function create_image($requestPath, $path, $width, $height)
    {
        $img = Image::make($requestPath)->resize($width, $height, function ($constraint) {
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

    public function save_photo()
    {
        request()->validate([
            'photos' =>  'required|mimes:jpeg,jpg,png,gif'
        ]);

        try {
            $image = request()->photos;
            $imageFullName = $image->getClientOriginalName();
            $imageName = pathinfo($imageFullName, PATHINFO_FILENAME);
            $imageExt = $image->getClientOriginalExtension();

            $xs = $imageName . 'xs' . bin2hex(time() . request()->user()->id) . '.' . $imageExt;
            $sm = $imageName . 'sm' . bin2hex(time() . request()->user()->id) . '.' . $imageExt;
            $md = $imageName . 'md' . bin2hex(time() . request()->user()->id) . '.' . $imageExt;
            $lg = $imageName . 'lg' . bin2hex(time() . request()->user()->id) . '.' . $imageExt;

            $xsmall = public_path('storage/photos/' . $xs);
            $small = public_path('storage/photos/' . $sm);
            $medium = public_path('storage/photos/' . $md);
            $large = public_path('storage/photos/' . $lg);


            $this->create_image(
                $image->getRealPath(),
                $xsmall,
                100,
                100
            );

            $this->create_image(
                $image->getRealPath(),
                $small,
                300,
                300
            );

            $this->create_image(
                $image->getRealPath(),
                $medium,
                600,
                500
            );

            $this->create_image(
                $image->getRealPath(),
                $large,
                800,
                700
            );

            $data = [];
            $data['xsmall'] = url('storage/photos/' . $xs);
            $data['small'] = url('storage/photos/' . $sm);
            $data['medium'] = url('storage/photos/' . $md);
            $data['larger'] = url('storage/photos/' . $lg);

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
    public function files()
    {
        request()->validate([
            'file' =>  'required|mimes:jpeg,jpg,png,mp4,gif,wmv,mov,flv,pdf'
        ]);
        try {
            $fileFullName = request()->file->getClientOriginalName();
            $fileName = pathinfo($fileFullName, PATHINFO_FILENAME);
            $fileExt = request()->file->getClientOriginalExtension();

            $storedName = $fileName . 'file' . bin2hex(time() . request()->user()->id) . '.' . $fileExt;

            $extensions = ['bmp', 'gif', 'jpeg', 'jpg', 'png', 'webp'];
            $url = url('storage/photos/' . $storedName);
            if (in_array($fileExt, $extensions)) {
                $filePath = public_path('storage/photos/' . $storedName);
                $this->create_image(
                    request()->file->getRealPath(),
                    $filePath,
                    400,
                    300
                );
            } else {
                request()->file('file')->move(public_path("storage/photos"), $storedName);
            }
            return $url;
        } catch (Exception $e) {
            //
        }
    }
}
