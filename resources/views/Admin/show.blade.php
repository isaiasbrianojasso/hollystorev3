<x-app-layout>
    <x-slot name="header">
        <h1>
            <a href="" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight"><svg
                    xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-list"
                    viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                        d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5" />
                </svg>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200 text-info">
            </a>
        </h1>
    </x-slot>
    <div class="row">

        <div class="col-md-12">

            <x-opciones></x-opciones>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade" id="pills-usuarios" role="tabpanel" aria-labelledby="pills-usuarios-tab"
                    tabindex="0">
                    <x-table-user>
                    </x-table-user>
                </div>

                <div class="tab-pane fade" id="pills-servicios" role="tabpanel" aria-labelledby="pills-profile-tab"
                    tabindex="0">
                    <x-table-servicios>
                    </x-table-servicios>
                </div>
                <div class="tab-pane fade" id="pills-api" role="tabpanel" aria-labelledby="pills-contact-tab"
                    tabindex="0">
                    <x-table-api>
                    </x-table-api>
                </div>
                <div class="tab-pane fade" id="pills-creditos" role="tabpanel" aria-labelledby="pills-disabled-tab"
                    tabindex="0">
                    <x-table-creditos></x-table-creditos>
                </div>

            </div>
        </div>
    </div>
    <x-sidebar />
    <x-modal />
    <x-script />
    <x-footer></x-footer>
</x-app-layout>
