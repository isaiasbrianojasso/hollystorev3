<x-app-layout>
    <x-slot name="header"><h1>
        <a href=""  data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
          </svg>
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200 text-info"></a></h1>
    </x-slot>
    <div class="container">

        <div class="row">

            <div class="col-md-12">
                <x-table-sms-gateway></x-table-sms-gateway>
            </div>
        </div>
    </div>
    <x-sidebar />

    <x-modal></x-modal>
    <footer class="py-3 mt-auto footer bg-light">
        <div class="container text-center">
            <span class="text-muted">© 2024 Hollydev</span>
            <div class="mt-2">
                <span class="text-muted">Fecha actual:
                    <?php echo date('Y-m-d'); ?>
                </span>
            </div>
        </div>
    </footer>
</x-app-layout>
