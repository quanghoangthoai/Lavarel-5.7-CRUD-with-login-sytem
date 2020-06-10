<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SoareCostin\FileVault\Facades\FileVault;
use View;
use Validator;
use App\Files;
use App\Jobs\EncryptFile;

class UploadController extends Controller
{
    public function index()
    {
        $files=Files::paginate(20);

    	return View::make('welcome',compact('files'));
    }

    public function uploadFile(Request $request)
    {
    	Validator::make($request->all(), [
    'file' => 'required',
    'file.*' => 'max:4000000000|mimes:doc,pdf,docx,zip,png,jpg,jpeg,gif,bmp,xls,xlms'
])->validate();

        $data=array();

        if($request->hasfile('file'))

         {


            foreach($request->file('file') as $file)

            {

                $name=$file->getClientOriginalName();

                $extension = $file->getClientOriginalExtension();

                $file->move(public_path().'/files/', $name);

                $data[] = $name;

                $filecheck= new Files();

                $filecheck->name=$name;

                $filecheck->extension=$extension;

                $filecheck->save();
                if ($file) {
                    EncryptFile::dispatch($file);
                }
            }

         }





    	return response ()->json (['status'=>'success','message'=>'File Uploaded Successfully','response'=>['data'=>$data]]);
    }

}
