<!doctype html>

<html>

<head>
    @include('templates.head')
</head>

<body
        @if (Session::has('success_message')) x-init="$nextTick(() => { trigger('success',  '{{ Session::get('success_message') }}' ) })"
        @endif
        @if (Session::has('error_message')) x-init="$nextTick(() => { trigger('error', '{{ Session::get('error_message') }}' ) })" @endif>
<div id="notification" x-data="{
        notices: [],
        visible: [],
        add(notice) {
            notice.id = Date.now()
            this.notices.push(notice)
            this.fire(notice.id)
        },
        fire(id) {
            this.visible.push(this.notices.find(notice => notice.id == id))
            const timeShown = 2000 * this.visible.length
            setTimeout(() => {
                this.remove(id)
            }, timeShown)
        },
        remove(id) {
            const notice = this.visible.find(notice => notice.id == id)
            const index = this.visible.indexOf(notice)
            this.visible.splice(index, 1)

            @php
            Session::forget('success_message');
            Session::forget('error_message');
            @endphp
        },
    }"
     class="z-[999999] p-7 fixed inset-0 w-screen flex flex-col-reverse items-end justify-end pointer-events-none"
     @notice.window="add($event.detail)">

    <template x-for="notice of notices" :key="notice.id">
        <div x-show="visible.includes(notice)" x-transition:enter="transition ease-in duration-200"
             x-transition:enter-start="transform opacity-0 translate-x-full"
             x-transition:enter-end="transform opacity-200" x-transition:leave="transition ease-out duration-200"
             x-transition:leave-start="transform translate-x-0 opacity-100"
             x-transition:leave-end="transform translate-x-full opacity-0" § @click="remove(notice.id)"
             :class="notice.type === 'success' ?
                    'rounded mb-4 p-3 w-full max-w-md text-black-800 shadow-lg cursor-pointer pointer-events-auto bg-green-200' :
                    'rounded mb-4 p-3 w-full max-w-md text-black-800 shadow-lg cursor-pointer pointer-events-auto bg-red-200'"
             x-text="notice.text">
        </div>
    </template>
</div>

@include('templates.navigation')

<div class="p-4 sm:ml-64">
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-20">
        <div id="main" class="max-w-screen-xl flex-wrap items-center justify-between mx-auto p-4">
            @yield('content')
        </div>

        <footer class="row"></footer>
    </div>
</div>

<script src="{{ URL::asset('js/jquery_3_7_1.min.js') }}"></script>
<script src="{{ URL::asset('js/datatables1_13_6.min.js') }}"></script>
<script src="{{ URL::asset('js/flowbite1_8_1.min.js') }}"></script>
<script src="{{ URL::asset('js/sweetalert2.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/custom.js') }}"></script>

<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.js"></script>


<script>
    function trigger(notification_type, message) {
        document.getElementById('notification').dispatchEvent(new CustomEvent('notice', {
            detail: {
                type: notification_type,
                text: message
            },
            bubbles: true
        }));
    }

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

            var url = $(this).attr('data-href');
            Swal.fire({
                title: "{{ __('common.alert') }}",
                icon: 'warning',
                text: "{{ __('common.delete_confirm') }}",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('common.yes') }}",
                cancelButtonText: "{{ __('common.cancel') }}",
            }).then((result) => {
                if (result.isConfirmed) {
                    var url = $(this).attr('data-href');
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
