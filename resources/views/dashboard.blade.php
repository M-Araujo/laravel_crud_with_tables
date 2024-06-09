@extends('templates.base')

@section('content')

    <div class="md:container md:mx-auto">

        <h2 class="text-4xl font-bold dark:text-white">Welcome</h2>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 p-4 gap-4">
            <div
                    class="bg-blue-500 dark:bg-gray-800 shadow-lg rounded-md flex items-center justify-between p-3 border-b-4 border-blue-600 dark:border-gray-600 text-white font-medium group">
                <div
                        class="flex justify-center items-center w-14 h-14 bg-white rounded-full transition-all duration-300 transform group-hover:rotate-12">


                    <svg class="h-8 w-8 stroke-current text-blue-500 dark:text-gray-800 "
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>


                </div>
                <div class="text-right">
                    <p class="text-2xl">Users</p>
                    <p>{{$stats['total_users']}}</p>
                </div>
            </div>
            <div
                    class="bg-blue-500 dark:bg-gray-800 shadow-lg rounded-md flex items-center justify-between p-3 border-b-4 border-blue-600 dark:border-gray-600 text-white font-medium group">
                <div
                        class="flex justify-center items-center w-14 h-14 bg-white rounded-full transition-all duration-300 transform group-hover:rotate-12">
                    {{-- <svg class="w-6 h-6 text-blue-800 dark:text-white" aria-hidden="true"
                          xmlns="http://www.w3.org/2000/svg"
                          fill="none" viewBox="0 0 16 20">
                         <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                               d="M11.5 8V4.5a3.5 3.5 0 1 0-7 0V8M8 12v3M2 8h12a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1Z"/>
                     </svg>  --}}

                    <svg class="h-8 w-8 text-blue-500" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                         stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"/>
                        <line x1="18" y1="6" x2="18" y2="6.01"/>
                        <path d="M18 13l-3.5 -5a4 4 0 1 1 7 0l-3.5 5"/>
                        <polyline points="10.5 4.75 9 4 3 7 3 20 9 17 15 20 21 17 21 15"/>
                        <line x1="9" y1="4" x2="9" y2="17"/>
                        <line x1="15" y1="15" x2="15" y2="20"/>
                    </svg>

                </div>
                <div class="text-right">
                    <p class="text-2xl">Countries</p>
                    <p>{{$stats['total_countries']}}</p>
                </div>
            </div>
            <div
                    class="bg-blue-500 dark:bg-gray-800 shadow-lg rounded-md flex items-center justify-between p-3 border-b-4 border-blue-600 dark:border-gray-600 text-white font-medium group">
                <div
                        class="flex justify-center items-center w-14 h-14 bg-white rounded-full transition-all duration-300 transform group-hover:rotate-12">

                    <svg class="h-8 w-8 text-blue-500" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                         stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"/>
                        <path d="M12 21a9 9 0 1 1 0 -18a9 8 0 0 1 9 8a4.5 4 0 0 1 -4.5 4h-2.5a2 2 0 0 0 -1 3.75a1.3 1.3 0 0 1 -1 2.25"/>
                        <circle cx="7.5" cy="10.5" r=".5" fill="currentColor"/>
                        <circle cx="12" cy="7.5" r=".5" fill="currentColor"/>
                        <circle cx="16.5" cy="10.5" r=".5" fill="currentColor"/>
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-2xl">Colours</p>
                    <p>{{$stats['total_colours']}}</p>
                </div>
            </div>
            <div
                    class="bg-blue-500 dark:bg-gray-800 shadow-lg rounded-md flex items-center justify-between p-3 border-b-4 border-blue-600 dark:border-gray-600 text-white font-medium group">
                <div
                        class="flex justify-center items-center w-14 h-14 bg-white rounded-full transition-all duration-300 transform group-hover:rotate-12">

                    <svg class="h-8 w-8 text-blue-500" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                         stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"/>
                        <circle cx="12" cy="12" r="9"/>
                        <line x1="9" y1="10" x2="9.01" y2="10"/>
                        <line x1="15" y1="10" x2="15.01" y2="10"/>
                        <path d="M9.5 15a3.5 3.5 0 0 0 5 0"/>
                        <path d="M12 3a2 2 0 0 0 0 4"/>
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-2xl">Has kids</p>
                    <p>{{$stats['has_kids']}}</p>
                </div>
            </div>

        </div>
        <!-- ./Statistics Cards -->


        <div class="container my-10 mx-auto md:px-6">
            <!-- Section: Design Block -->
            <section class="mb-32 text-center">
                <div class="grid md:grid-cols-3 lg:gap-x-12">
                    <div class="mb-12 md:mb-0">

                        <h5 class="text-lg font-medium text-neutral-500 dark:text-neutral-300">
                            User´s colours
                        </h5>

                        <x-pie-chart :data="$stats['user_colours']" :id="1"/>
                    </div>

                    <div class="mb-12 md:mb-0">


                        <h5 class="text-lg font-medium text-neutral-500 dark:text-neutral-300">
                            User´s countries
                        </h5>

                        <x-pie-chart :data="$stats['user_countries']" :id="2"/>
                    </div>

                    <div class="mb-12 md:mb-0">
                        <h5 class="text-lg font-medium text-neutral-500 dark:text-neutral-300">
                            Users with kids
                        </h5>

                        <x-donut-chart :data="$stats['has_kids_chartdata']" :id="3"/>
                    </div>
                </div>
            </section>
            <!-- Section: Design Block -->
        </div>
        @stop

        @section('scripts')
            <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>