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

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">

                <div
                    class="p-6 bg-white border-b border-gray-200 lg:p-8 dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent dark:border-gray-700">

                    <div class="row">
                        <div class="alert alert-primary" role="alert">
                            <strong>HollyDev Services <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-box-fill" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M15.528 2.973a.75.75 0 0 1 .472.696v8.662a.75.75 0 0 1-.472.696l-7.25 2.9a.75.75 0 0 1-.557 0l-7.25-2.9A.75.75 0 0 1 0 12.331V3.669a.75.75 0 0 1 .471-.696L7.443.184l.004-.001.274-.11a.75.75 0 0 1 .558 0l.274.11.004.001zm-1.374.527L8 5.962 1.846 3.5 1 3.839v.4l6.5 2.6v7.922l.5.2.5-.2V6.84l6.5-2.6v-.4l-.846-.339Z" />
                                </svg></strong>
                        </div>
                        @foreach (App\Models\Servicio::all() as $servicio)
                        @if($servicio->api=="apple_remove"  || $servicio->api=="apple_check")
                        <div class="m-1 col-md-3">
                            <div class="text-left card" style="border-radius: 40px 40px;">
                                <img class="card-img-top"
                                    src="https://i5.walmartimages.com/seo/Pre-Owned-Apple-iPhone-X-64GB-Factory-Unlocked-Smartphone-Refurbished-Good_9b5ec8b2-9665-463b-adc5-64829ba72da6.1b496e5a8fcee76fdad69bae12b54745.jpeg"
                                    alt="">
                                <div class="card-body">
                                    <div class="card-title">
                                        Service <span class="mb-1 badge text-bg-secondary"></span>

                                    </div>

                                    <h4 class="card-title">{{$servicio->nombre}}</h4>
                                    <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                                        <div class="btn-group me-2" role="group" aria-label="First group">
                                            <button type="button" class="btn btn-success" style=" border: none;
  border-radius: 40px 10px;
  background: gold;">{{$servicio->api}}</button>
                                            <button style=" border: none;
  border-radius: 20px 10px;
  background: purple;" type="button" class="btn btn-info">{{$servicio->tipo}}
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-box-fill" viewBox="0 0 16 16">
                                                    <path fill-rule="evenodd"
                                                        d="M15.528 2.973a.75.75 0 0 1 .472.696v8.662a.75.75 0 0 1-.472.696l-7.25 2.9a.75.75 0 0 1-.557 0l-7.25-2.9A.75.75 0 0 1 0 12.331V3.669a.75.75 0 0 1 .471-.696L7.443.184l.004-.001.274-.11a.75.75 0 0 1 .558 0l.274.11.004.001zm-1.374.527L8 5.962 1.846 3.5 1 3.839v.4l6.5 2.6v7.922l.5.2.5-.2V6.84l6.5-2.6v-.4l-.846-.339Z" />
                                                </svg></button>


                                        </div>
                                    </div>

                                    <p class="card-text">{{$servicio->descripcion}}</p>

                                    <div class="card-footer">
                                        @if($servicio->status==0)
                                        <button type="button" class="btn btn-primary ">Buy</button>
                                        <button type="button" class="btn btn-primary ">Demo</button>

                                        @else
                                        <button type=" button" class="btn-secondary" disabled>Buyed</button>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <x-footer></x-footer>
    <x-sidebar />
</x-app-layout>
