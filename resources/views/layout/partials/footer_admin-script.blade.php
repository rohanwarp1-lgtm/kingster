{{-- All libraries first, then admin.js, then inline init --}}
<script src="{{ asset('admin_assets/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('admin_assets/js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('admin_assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('admin_assets/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('admin_assets/plugins/apexchart/apexcharts.min.js') }}"></script>
<script src="{{ asset('admin_assets/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('admin_assets/js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ asset('admin_assets/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin_assets/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('admin_assets/js/select2.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.12/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ asset('admin_assets/js/admin.js') }}"></script>

<script>
    (function ($) {
        if (!$.fn.modal && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            $.fn.modal = function (action) {
                return this.each(function () {
                    const instance = bootstrap.Modal.getOrCreateInstance(this);
                    if (action === 'show') instance.show();
                    if (action === 'hide') instance.hide();
                    if (action === 'toggle') instance.toggle();
                });
            };
        }
        $(document).on('click', '[data-dismiss="modal"]', function () {
            $(this).closest('.modal').modal('hide');
        });
    })(jQuery);

    @if(session('success'))
        toastr.success('{{ session('success') }}');
    @endif
    @if(session('error'))
        toastr.error('{{ session('error') }}');
    @endif
    @if(session('warning'))
        toastr.warning('{{ session('warning') }}');
    @endif
    @if(session('info'))
        toastr.info('{{ session('info') }}');
    @endif

    $(document).ready(function () {
        $('.datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            icons: {
                time: 'fe fe-clock', date: 'fe fe-calendar',
                up: 'fe fe-chevron-up', down: 'fe fe-chevron-down',
                previous: 'fe fe-chevron-left', next: 'fe fe-chevron-right',
                today: 'fe fe-calendar-check', clear: 'fe fe-trash-2', close: 'fe fe-x'
            }
        });
    });
</script>
