@extends('templates.base')

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
                            focus:ring-green-300 font-medium rounded-lg text-sm px-2 py-1.5 mr-2 mb-2 dark:bg-green-600
                            dark:hover:bg-green-700 dark:focus:ring-green-900 mt-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"/>
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
