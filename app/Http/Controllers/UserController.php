<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{

    public function index(Request $request)
    {
        $items = User::select('id', 'name', 'email', 'picture')->get();
        return view('users.list')->with(compact('items'));
    }

    public function edit($id)
    {
        $item = User::find($id);
        return view('users.edit')->with(compact('item'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), $this->updateRequest()->rules());

        if ($validator->fails()) {
            Session::put('error_message', __('common.error'));
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $old_images = [];
        $data = $request->only(['name', 'email', 'picture']);

        $item = User::find($id);

        $data = data_forget($data, $this->imagesAttributes());
        if (is_countable($this->imagesAttributes())) {
            foreach ($this->imagesAttributes() as $attribute) {
                if ($request->hasFile($attribute)) {
                    $old_images[$attribute] = $item->getRawOriginal($attribute);
                }
            }
        }

        DB::beginTransaction();

        try {
            $item->update($data);
            DB::commit();
            if (is_countable($this->imagesAttributes())) {
                foreach ($this->imagesAttributes() as $attribute) {
                    if ($request->hasFile($attribute)) {
                        $this->updateImage($request, $item, $old_images, $attribute);
                    }
                }
            }

            Session::put('success_message', 'Item updated with success.');
            return redirect('/users');
        } catch (Exception $e) {
            DB::rollback();
            Session::put('error_message', 'Oops, something is wrong.');
            return redirect()->back()->withInput();
        }
    }

    protected function updateRequest()
    {
        return new UpdateUserRequest;
    }

    protected function imagesAttributes()
    {
        return ['picture'];
    }

    public function updateImage($request, $item, $old_images, $attribute)
    {
        $fileNameWithExt = $request->file($attribute)->getClientOriginalName();
        $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
        $extension = $request->file($attribute)->getClientOriginalExtension();
        $fileNameToStore = $filename . '_' . time() . '.' . $extension;

        $request->$attribute->move(public_path($this->filePath()), $fileNameToStore);
        $item[$attribute] = $fileNameToStore;
        $item->save();

        if ($old_images[$attribute]) {
            $oldPhotoPath = public_path() . '/' . $this->filePath() . '/' . $old_images[$attribute];
            if (file_exists($oldPhotoPath)) {
                unlink($oldPhotoPath);
            }
        }
    }

    protected function filePath()
    {
        return '';
    }
}
