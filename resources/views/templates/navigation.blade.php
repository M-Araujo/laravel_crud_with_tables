@include('templates.navbar')

<aside id="logo-sidebar"
       class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
       aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
        <ul class="space-y-2 font-medium" id="sidebar_navigation">
            <li>

            <li>
                <a id='/users' href="/users"
                   class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                    <svg class="h-6 w-6 text-gray-500" width="24" height="24" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"/>
                        <polyline points="21 12 17 12 14 20 10 4 7 12 3 12"/>
                    </svg>
                    <span class="flex-1 ml-3 whitespace-nowrap">Users</span>
                </a>
            </li>

            <li>
                <a id=''
                   class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                    <svg class="h-6 w-6 text-gray-500" width="24" height="24" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"/>
                        <rect x="3" y="7" width="18" height="13" rx="2"/>
                        <path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2"/>
                        <line x1="12" y1="12" x2="12" y2="12.01"/>
                        <path d="M3 13a20 20 0 0 0 18 0"/>
                    </svg>
                    <span class="flex-1 ml-3 whitespace-nowrap">Dummy list 2</span>
                </a>
            </li>
        </ul>
    </div>
</aside>
