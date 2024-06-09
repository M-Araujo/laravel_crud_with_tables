<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\EditUserRequest;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
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

    public function index(Request $request): Factory|\Illuminate\Foundation\Application|View|Application
    {
        $items = User::select('id', 'name', 'email', 'picture')->get();
        return view('users.list')->with(compact('items'));
    }

    public function edit($id): Factory|\Illuminate\Foundation\Application|View|Application
    {
        $item = User::find($id);
        return view('users.edit')->with(compact('item'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->createValidatorRequest()->rules());

        if ($validator->fails()) {
            Session::put('error_message', 'Oops, something is wrong.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'email', 'password', 'has_kids']);
        DB::beginTransaction();

        try {
            $item = User::create($data);
            $this->insertOrUpdateImages($request, $item, $old_images = []);
            DB::commit();

            Session::put('success_message', 'Item updated with success.');
            return redirect('/users');
        } catch (Exception $e) {
            DB::rollback();

            Session::put('error_message', 'Oops, something is wrong.');
            return redirect()->back()->withInput();
        }
    }

    protected function createValidatorRequest(): CreateUserRequest
    {
        return new CreateUserRequest;
    }

    public function create(): Factory|\Illuminate\Foundation\Application|View|Application
    {
        return view('users.edit');
    }

    public function insertOrUpdateImages($request, $item, $old_images): void
    {
        if (is_countable($this->modelImages())) {
            foreach ($this->modelImages() as $attribute) {
                if ($request->hasFile($attribute)) {

                    $old_images[$attribute] = $item->getRawOriginal($attribute);

                    $filename = time() . '.' . $request->file('picture')->getClientOriginalExtension();
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
            }
        }
    }

    protected function modelImages(): array
    {
        return ['picture'];
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), $this->updateValidatorRequest()->rules());

        if ($validator->fails()) {
            Session::put('error_message', 'Oops, something is wrong.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $old_images = [];
        $data = $request->only(['name', 'email', 'picture', 'has_kids']);
        $item = User::find($id);
        $data = data_forget($data, $this->modelImages());

        if (is_countable($this->modelImages())) {
            foreach ($this->modelImages() as $attribute) {
                if ($request->hasFile($attribute)) {
                    $old_images[$attribute] = $item->getRawOriginal($attribute);
                }
            }
        }

        DB::beginTransaction();

        try {
            $item->update($data);
            $this->insertOrUpdateImages($request, $item, $old_images);

            DB::commit();

            Session::put('success_message', 'Item updated with success.');
            return redirect('/users');
        } catch (Exception $e) {
            DB::rollback();

            Session::put('error_message', 'Oops, something is wrong.');
            return redirect()->back()->withInput();
        }
    }

    protected function updateValidatorRequest(): EditUserRequest
    {
        return new EditUserRequest;
    }

    public function destroy($id)
    {
        $item = User::find($id);

        if ($item) {
            if (is_countable($this->modelImages()) && count($this->modelImages()) > 0) {
                foreach ($this->modelImages() as $attribute) {
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
