@php use Illuminate\Support\Facades\Session; @endphp
        <!doctype html>

<html>

<head>
    @include('templates.head')
</head>

<body>

@include('templates.navigation')

<div class="p-4 sm:ml-64">
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-20">
        <div id="main" class="max-w-screen-xl flex-wrap items-center justify-between mx-auto p-4">
            @yield('content')
        </div>

        <footer class="row"></footer>
    </div>
</div>
<script type="text/javascript" src="{{ URL::asset('js/jquery_3_7_1.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/datatables1_13_6.min.js') }}"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.js"></script>
<script src="{{ URL::asset('js/sweetalert2.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/flowbite1_8_1.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/dropify.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/custom.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
        integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
      integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA=="
      crossorigin="anonymous" referrerpolicy="no-referrer"/>
<script>
    $('.dropify').dropify();

    @if (Session::has('success_message'))

    toastr.success("{{Session::get('success_message')}}");
    @elseif(Session::has('error_message'))

    toastr.error("{{Session::get('error_message')}}");
    @endif

    {{Session::forget('success_message')}}
    {{Session::forget('error_message')}}


    $(document).ready(function () {
        var table = $('#example').dataTable({
            responsive: true,
            colReorder: true,
            "deferRender": true,
            "lengthChange": false,
            "language": {
                // not necessary here
            }
        });

        table.on('click', '.delete_btn', function () {

            let url = $(this).attr('data-href');
            console.log(url);

            Swal.fire({
                title: "Alert",
                icon: 'warning',
                text: "Are you sure?",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "Yes",
                cancelButtonText: "No",
            }).then((result) => {
                if (result.isConfirmed) {
                    // let url = $(this).attr('data-href');
                    $.ajax({
                        type: 'DELETE',
                        url: url,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (data) {
                            location.reload();
                        }
                    });
                }
            })
        });
    });
</script>

</body>

</html>
