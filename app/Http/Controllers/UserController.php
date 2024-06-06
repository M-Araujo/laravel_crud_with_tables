<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Image;


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
            Session::put('error_message', 'Oops, ocorreu um erro.');
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

    protected function updateRequest(): UpdateUserRequest
    {
        return new UpdateUserRequest;
    }

    protected function imagesAttributes(): array
    {
        return ['picture'];
    }


    public function updateImage($request, $item, $old_images, $attribute): void
    {
        if (is_countable($this->imagesAttributes())) {
            foreach ($this->imagesAttributes() as $attribute) {
                if ($request->hasFile($attribute)) {
                    $old_images[$attribute] = $item->getRawOriginal($attribute); // igual ao da bd
                }
            }
        }

        $filename = time() . '.' . $request->file('picture')->getClientOriginalExtension();

        // in storage
        $path = $request->file('picture')->storeAs('public/users', $filename);

        $item[$attribute] = $path;
        $item->save();

        if ($old_images[$attribute]) {
            $oldPhotoPath = $old_images[$attribute];
            if (Storage::exists($oldPhotoPath)) {
                Storage::delete($oldPhotoPath);
            }
        }
    }

    public function destroy($id)
    {
        $item = User::find($id);

        if ($item) {
            if (is_countable($this->imagesAttributes()) && count($this->imagesAttributes()) > 0) {
                foreach ($this->imagesAttributes() as $attribute) {
                    $file_path = public_path() . '/' . $this->filePath() . '/' . $item->getRawOriginal($attribute);
                    if (!is_dir($file_path) && file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
            }
            $item->delete();
            Session::put('success_message', 'Item deleted with success.');
        }
        return Response::json(['status' => 200], 200);
    }

    protected function filePath(): string
    {
        return '';
    }
}
