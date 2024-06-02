<meta charset="utf-8">

<meta name="description" content="">

<meta name="" content="Blade">

<title></title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}" />

<link rel="stylesheet" href="{{ URL::asset('css/flowbite1_4_5.min.css') }}" />
<link rel="stylesheet" href="{{ URL::asset('css/tw_elements1_0_0.min.css') }}" />
<link rel="stylesheet" href="{{ URL::asset('css/boxicons2_1_2.min.css') }}" />
<link rel="stylesheet" href="{{ URL::asset('css/datatables_1_13_6.min.css') }}" />
<link rel="stylesheet" href="{{ URL::asset('css/dropify.min.css') }}" />
<link rel="stylesheet" href="{{ URL::asset('css/select2.min.css') }}" />

<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap.css" />


@vite('resources/css/app.css')
@vite('resources/js/app.js')


<style>
    .invalid {
        border-color: darkred;
        background-color: hsl(0, 30%, 95%);
        margin-bottom: 0em;
    }

    .error-message {
        margin-bottom: 1em;
        color: hsl(0deg, 100%, 15%);
    }
</style>
