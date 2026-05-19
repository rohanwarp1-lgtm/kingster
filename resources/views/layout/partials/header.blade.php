<?php error_reporting(0); ?>

<style>
    .header.header-one .topheaderbar {
        opacity: 1;
        visibility: visible;
        transition: opacity 0.5s ease, visibility 0.5s ease;
        height: auto;
        display: block !important;
    }
    
    .header.header-one.fixed .topheaderbar {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        display: none !important;
    }

</style>

<header class="header header-one">
    <div class="container topheaderbar border-bottom px-0">
        <div class="py-2 d-flex justify-content-between">
            <div class="first">
                <span class="fs-13 text-dark fw-200">
                    Customer Support: 
                    <a href="tel:{{ isset($generalSettings) && $generalSettings->customer_support_mobile ? $generalSettings->customer_support_mobile : '+918866513744' }}">
                        {{ isset($generalSettings) && $generalSettings->customer_support_mobile ? $generalSettings->customer_support_mobile : '+91 88665 13744' }}
                    </a> 
                    |
                    <a href="mailto:{{ isset($generalSettings) && $generalSettings->customer_support_email ? $generalSettings->customer_support_email : 'support@kingster.info' }}">
                        {{ isset($generalSettings) && $generalSettings->customer_support_email ? $generalSettings->customer_support_email : 'support@kingster.info' }}
                    </a>
                </span>
            </div>
            <div class="second">
                <a href="{{ route('login') }}" class="fs-13 text-dark fw-200">Admin Login</a>
            </div>
        </div>
    </div>
    <div class="container">

        <nav class="navbar navbar-expand-lg header-nav">
            <div class="navbar-header">
                <a href="{{url('/')}}" class="navbar-brand logo">
                    @if(isset($generalSettings) && $generalSettings->brand_logo)
                        <img src="@prodImage($generalSettings->brand_logo)" class="img-fluid" alt="Logo">
                    @else
                        <img src="@prodImage('assets/img/kingster-logo.png')" class="img-fluid" alt="Logo">
                    @endif
                </a>
                <a href="{{url('/')}}" class="navbar-brand logo-small">
                    @if(isset($generalSettings) && $generalSettings->brand_white_logo)
                        <img src="@prodImage($generalSettings->brand_white_logo)" class="img-fluid" alt="Logo">
                    @else
                        <img src="@prodImage('assets/img/kingster-logo.png')" class="img-fluid" alt="Logo">
                    @endif
                </a>
                <a id="mobile_btn" href="javascript:void(0);">
                    <span class="bar-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </a>
            </div>
            <div class="main-menu-wrapper">
                <div class="menu-header">
                    <a href="{{url('/')}}" class="menu-logo">
                        @if(isset($generalSettings) && $generalSettings->brand_logo)
                            <img src="@prodImage($generalSettings->brand_logo)" class="img-fluid" alt="Logo">
                        @else
                            <img src="@prodImage('assets/img/kingster-logo.png')" class="img-fluid" alt="Logo">
                        @endif
                    </a>
                    <a id="menu_close" class="menu-close" href="javascript:void(0);"> <i class="fas fa-times"></i></a>
                </div>

                @php
                    $staticProductLinks = [
                        ['name' => 'External Hard Disk',    'route' => 'ssd',         'page' => 'kingster-hard-disk'],
                        ['name' => 'Monitor Display',       'route' => 'monitor',     'page' => 'kingster-monitor'],
                        ['name' => 'TWS Earbuds',           'route' => 'airbuds',     'page' => 'kingster-airbuds'],
                        ['name' => 'Gaming Keyboard',       'route' => 'keyboard',    'page' => 'kingster-keyboard'],
                        ['name' => '360 Laptop Stand',      'route' => 'laptop.stand','page' => 'kingster-laptop-stand'],
                        ['name' => 'Flash Drive',           'route' => 'pendrive',    'page' => 'kingster-pendrive'],
                    ];
                    $staticPages = array_column($staticProductLinks, 'page');
                    $dbHasProducts = isset($products) && $products->count() > 0;
                    $isProductPage = $page == 'product-details'
                        || in_array($page, $staticPages);
                @endphp

                <ul class="main-nav align-items-lg-center">
                    <li class=" <?php if ($page == '/' || $page == 'index-3') { echo 'active'; } ?>">
                        <a href="{{ url('/') }}">Home</a>
                    </li>

                    <li class="has-submenu {{ $isProductPage ? 'active' : '' }}">
                        <a href="javascript:void(0);">Our Products <i class="fas fa-chevron-down"></i></a>
                        <ul class="submenu">
                            @if($dbHasProducts)
                                @foreach($products as $product)
                                    <li class="{{ ($page == 'product-details' && request()->segment(2) == $product->id) ? 'active' : '' }}">
                                        <a href="{{ url('/product/' . $product->id) }}">{{ $product->product_name }}</a>
                                    </li>
                                @endforeach
                            @else
                                @foreach($staticProductLinks as $sp)
                                    <li class="{{ $page == $sp['page'] ? 'active' : '' }}">
                                        <a href="{{ route($sp['route']) }}">{{ $sp['name'] }}</a>
                                    </li>
                                @endforeach
                            @endif
                            <li style="border-top: 1px solid #130b377d;">
                                <a href="{{ route('products') }}" class="text-center">View All Products</a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="has-submenu  <?php if ($page == 'warranty-application-form' || $page == 'warranty-status-lookup' || $page == 'product-replacement-policy') {echo 'active'; } ?>">
                        <a href="javascript:void(0);">Warranty Services <i class="fas fa-chevron-down"></i></a>
                        <ul class="submenu">
                            <li class="<?php if ($page == 'warranty-application-form') {echo 'active';} ?>"><a href="{{url('/warranty-application-form')}}">Warranty Registration</a></li>
                            <li class="<?php if ($page == 'warranty-status-lookup') {echo 'active';} ?>"><a href="{{url('/warranty-status-lookup')}}">Warranty Status Lookup</a></li>
                            <li class="<?php if ($page == 'product-replacement-policy') {echo 'active';} ?>"><a href="{{ url('/replacement-policy') }}">Replacement Policy</a></li>
                        </ul>
                    </li>

                    <li class=" <?php if ($page == 'contact-us') { echo 'active'; } ?>">
                        <a href="{{ url('contact-us') }}">Contact Us</a>
                    </li>

                </ul>
            </div>
        </nav>

    </div>
</header>

