@extends('templates.base')

@section('content')

    <div class="md:container md:mx-auto">

        <div class="flex justify-between mb-10">
            <h2 class="text-4xl font-bold dark:text-white">Users</h2>
            
            <a href="users/create"
               class="px-6 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-900">Create
            </a>

        </div>
        <table id="table" class="display" style="width:100%">
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
                        <div class="relative  overflow-hidden rounded-full dark:bg-gray-600">
                            <img class="w-10 h-10 rounded-full"
                                 src="{{ $user['picture'] }}" alt="user photo">
                        </div>
                    </td>
                    <td>{{ $user['name'] }}</td>
                    <td>{{ $user['email'] }}</td>

                    <td>
                        <a href="users/{{ $user['id'] }}/edit" type="button"
                           class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4
                            focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2 mr-2 mb-2 dark:bg-green-600
                            dark:hover:bg-green-700 dark:focus:ring-green-900 mt-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 26 26" stroke-width="1.5"
                                 stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"/>
                            </svg>
                        </a>

                        <a type="button" data-href="users/{{ $user['id'] }}"
                           class="delete_btn focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4
            focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-red-600
            dark:hover:bg-red-700 dark:focus:ring-red-900 mt-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                 class="w-5 h-5">
                                <path fill-rule="evenodd"
                                      d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z"
                                      clip-rule="evenodd"/>
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
