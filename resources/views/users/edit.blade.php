@extends('templates.base')

@section('content')

    <div class="md:container md:mx-auto">

        <h2 class="text-4xl font-bold dark:text-white">  @isset($item)
                Edit user
            @else
                Create user
            @endisset
        </h2>

        @isset($item)
            <form action="{{ route('users.update',$item->id) }}" method="POST" enctype="multipart/form-data">
                @method('PUT')
                @else
                    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data"
                          autocomplete="off">

                        @endisset
                        @csrf


                        <div class="grid grid-cols-1 gap-2 md:grid-cols-2 pt-6">

                            <div>
                                <div>
                                    <label for="username-success"
                                           class="block mb-1 text-sm font-medium text-700">User name</label>
                                    <input type="text" @isset($item) value="{{ $item->name }}" @endisset name="name"
                                           class=" border text-sm rounded-lg block w-full p-2.5">

                                    @error('name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="pt-4">
                                    <label for="email-success"
                                           class="block mb-1 text-sm font-medium text-700">Email</label>
                                    <input type="text" @isset($item) value="{{ $item->email }}" @endisset name="email"
                                           class=" border text-sm rounded-lg block w-full p-2.5">
                                    @error('email')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>


                                <div class="pt-4">
                                    <label for="image-success"
                                           class="block mb-2 text-sm font-medium text-700">Image</label>
                                    <div class="bg-slate-50 h-30 w-30 p-3 border rounded-lg ">

                                        <div class="relative flex items-center justify-center overflow-hidden dark:bg-gray-600 ">
                                            <input type="file" class="dropify mx-auto"
                                                   @isset($item)
                                                       data-default-file="{{ $item->picture }}"
                                                   @endisset
                                                   name="picture" data-height="250"/>

                                            @error('picture')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div>

                                <div>
                                    <label for="has_kids-success"
                                           class="block mb-1 text-sm font-medium text-700">Has kids?</label>
                                    <select id="has_kids" name="has_kids"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        <option selected>Choose an option</option>

                                        @php
                                            $options = [
                                            ['value' => 0, 'name' => 'No'],
                                            ['value' => 1, 'name' => 'Yes']
                                            ]
                                        @endphp
                                        @foreach( $options as $option)
                                            <option
                                                    value={{$option['value']}}
                                            @isset($item)
                                            @if($item->has_kids == $option['value'])
                                            selected
                                                    @endif
                                                    @endisset
                                            >{{$option['name']}}</option>
                                        @endforeach

                                    </select>
                                    @error('has_kids')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror

                                </div>


                                <div class="pt-4">
                                    <label for="password-success"
                                           class="block mb-1 text-sm font-medium text-700">Password</label>
                                    <input type="password" name="password"
                                           class=" border text-sm rounded-lg block w-full p-2.5">
                                    @error('password')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror

                                </div>


                                <div class="pt-4">
                                    <label for="password-confirmation-success"
                                           class="block mb-1 text-sm font-medium text-700">Password confirmation</label>
                                    <input type="password" name="password_confirm"
                                           class=" border text-sm rounded-lg block w-full p-2.5">
                                    @error('password_confirm')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="pt-4">
                                    <hr>
                                    <small class="mb-2 font-bold">*The password field must have at least:</small> <br/>
                                    <ul class="list-disc text-xs list-inside">
                                        <li>Minimum length of 8 characters</li>
                                        <li>Maximum length of 35 characters</li>
                                        <li>Must contain at least one lowercase letter</li>
                                        <li>Must contain at least one uppercase letter</li>
                                        <li>Must contain at least one digit</li>
                                        <li>Must contain a special character</li>
                                    </ul>
                                    </small>
                                </div>
                            </div>


                            <div class="flex justify-end mt-5">
                                <button type="submit"
                                        class="px-6 py-2 mt-4 text-white bg-blue-600 rounded-lg hover:bg-blue-900">
                                    Submit
                                </button>
                            </div>

                        </div>
                    </form>
    </div>

@stop
