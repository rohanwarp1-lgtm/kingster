<!DOCTYPE html>
<html lang="en">

<head>
    @include('layout.partials.head_admin')
</head>

<body>
    <div class="main-wrapper">
        @include('layout.partials.header_admin')
        @include('layout.partials.sidebar_admin')
        <div class="sidebar-overlay" id="sidebar-overlay"></div>
        @yield('content')
    </div>

    <script>
        var ajaxURL            = "{{ route('warranty.ajax') }}";
        var changeWarrantyStatusURL = "{{ route('warranty.change.status') }}";
        var deleteWarrantyURL  = "{{ route('delete.warranty') }}";
        var restoreWarrantyURL = "{{ route('restore.warranty') }}";

        var productIndexUrl    = "{{ route('product.index') }}";
        var createProductView  = "{{ route('create.product.view') }}";
        var productStore       = "{{ route('product.store') }}";
        var productAjax        = "{{ route('product.ajax') }}";
        var productDeleteURL   = "{{ route('product.delete') }}";
        var productRestoreURL  = "{{ route('product.restore') }}";

        var userAjaxURL        = "{{ route('user.ajax') }}";
        var userSaveURL        = "{{ route('user.save') }}";
        var getUserDetails     = "{{ route('get.user.details') }}";
        var userDeleteURL      = "{{ route('delete.user') }}";
        var userRestoreURL     = "{{ route('restore.user') }}";

        var productNameAjaxURL    = "{{ route('product.name.ajax') }}";
        var productNameSaveURL    = "{{ route('product.name.store') }}";
        var getProductNameDetailURL = "{{ route('product.name.details') }}";
        var deleteProductNameURL  = "{{ route('delete.product.name') }}";
        var restoreProductNameURL = "{{ route('restore.product.name') }}";

        var generalSettingSave    = "{{ route('general.setting.save') }}";
        var deleteProductImageURL = "{{ route('delete.product.image') }}";
    </script>

    @include('layout.partials.footer_admin-script')

    @stack('scripts')
    @yield('prouctpage-js')
</body>

</html>
