@extends('templates.base')

@section('content')

    <div class="md:container md:mx-auto">

        <h2 class="text-4xl font-bold dark:text-white">Edit user</h2>

        <form action="{{ route('users.update',$item->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-2 md:grid-cols-2 pt-6">

                <div>
                    <div>
                        <label for="username-success"
                               class="block mb-1 text-sm font-medium text-700">User name</label>
                        <input type="text" value="{{ $item->name }}" name="name"
                               class=" border text-sm rounded-lg block w-full p-2.5">

                        @error('name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="pt-4">
                        <label for="username-success"
                               class="block mb-1 text-sm font-medium text-700">Email</label>
                        <input type="text" value="{{ $item->email }}" name="email"
                               class=" border text-sm rounded-lg block w-full p-2.5">
                        @error('email')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <div>
                    <label for="username-success"
                           class="block mb-2 text-sm font-medium text-700">Image</label>
                    <div class="bg-slate-50 h-30 w-30 p-3 border rounded-lg ">

                        <div class="relative flex items-center justify-center overflow-hidden dark:bg-gray-600 ">
                            <input type="file" class="dropify mx-auto"
                                   data-default-file="{{ $item->picture }}"
                                   name="picture" data-height="250"/>
                        </div>
                    </div>
                </div>


                <div class="flex justify-end mt-5">
                    <button type="submit"
                            class="px-6 py-2 mt-4 text-white bg-blue-600 rounded-lg hover:bg-blue-900">Submit
                    </button>
                </div>

            </div>
        </form>
    </div>

@stop
