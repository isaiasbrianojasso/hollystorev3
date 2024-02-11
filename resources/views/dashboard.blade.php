<x-app-layout>
    <x-slot name="header" >
        <h1 >
            <a href="" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight"><svg
                    xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-list"
                    viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                        d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5" />
                </svg>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200 text-info">Welcome {{Auth::user()->name}}</h2>
            </a>
        </h1>
    </x-slot>

    <div class="py-12" >
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8" >
            <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg" >
                <x-dashboard />
            </div>
        </div>
    </div>
    <x-footer></x-footer>
    <x-sidebar />
</x-app-layout>
