@extends('templates.table')

@section('content')

    <div class="md:container md:mx-auto">

        <h2 class="text-4xl font-bold dark:text-white">Users</h2>
        <table id="example" class="display" style="width:100%">
            <thead>
            <tr>
                <th>Id</th>
                <th>Photo</th>
                <th>Name</th>
                <th>Email</th>
                <th>Options</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($items as $user)
                <tr>
                    <td>{{ $user['id'] }}</td>
                    <td>
                        <div class="relative w-12 h-12 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                            <img class="object-fill p-1 rounded-full ring-2 ring-gray-300 dark:ring-gray-500"
                                 src="{{ $user['picture'] }}" alt="user photo">
                        </div>
                    </td>
                    <td>{{ $user['name'] }}</td>
                    <td>{{ $user['email'] }}</td>

                    <td>
                        <a href="users/{{ $user['id'] }}/edit" type="button"
                           class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4
                            focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-green-600
                            dark:hover:bg-green-700 dark:focus:ring-green-900 mt-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </a>
                    </td>
                </tr>
            @endforeach

            </tbody>
            <tfoot>
            <tr>
                <th>Id</th>
                <th>Photo</th>
                <th>Name</th>
                <th>Email</th>
                <th>Options</th>
            </tr>
            </tfoot>
        </table>
@stop

@section('scripts')
