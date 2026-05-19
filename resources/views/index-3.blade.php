<?php $page = 'index-3'; ?>
@extends('layout.mainlayout')
@section('content')

<!-- Banner section -->
<section class="hero-section-two py-0">
    <div class="banner-slider slider text-center">
        @if(isset($generalSettings))
            @if($generalSettings->header_banner_1)
                <div class="banner">
                    <img class="img-fluid" src="@prodImage($generalSettings->header_banner_1)" alt="Banner 1">
                </div>
            @endif
            @if($generalSettings->header_banner_2)
                <div class="banner">
                    <img class="img-fluid" src="@prodImage($generalSettings->header_banner_2)" alt="Banner 2">
                </div>
            @endif
            @if($generalSettings->header_banner_3)
                <div class="banner">
                    <img class="img-fluid" src="@prodImage($generalSettings->header_banner_3)" alt="Banner 3">
                </div>
            @endif
            @if($generalSettings->header_banner_4)
                <div class="banner">
                    <img class="img-fluid" src="@prodImage($generalSettings->header_banner_4)" alt="Banner 4">
                </div>
            @endif
        @else
            <div class="banner">
                <img class="img-fluid" src="@prodImage('assets/img/banner1.jpg')" alt="img">
            </div>
            <div class="banner">
                <img class="img-fluid" src="@prodImage('assets/img/banner2.jpg')" alt="img">
            </div>
            <div class="banner">
                <img class="img-fluid" src="@prodImage('assets/img/banner3.jpg')" alt="img">
            </div>
        @endif
    </div>
</section>


<section class="journey-nine-section section">
    <div class="container">
        <div class="row justify-content-between g-4">
            <div class="col-lg-3">
                <div class="section-heading section-heading-nine journey-heading-nine aos" data-aos="fade-up">
                    <h2 class="text-white">It's Our <span>journey</span></h2>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 d-flex">
                <div class="journey-client-all aos" data-aos="fade-up">
                    <div class="journey-counter">
                        <h2><span class="home-counter">{{ $generalSettings->active_clients ?? 0 }}</span>+</h2>
                        <p>Active Clients</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 d-flex">
                <div class="journey-client-all aos" data-aos="fade-up">
                    <div class="journey-counter">
                        <h2><span class="home-counter">{{ $generalSettings->expert_mechanics ?? 0 }}</span>+</h2>
                        <p>Expert Mechanics</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 d-flex">
                <div class="journey-client-all aos" data-aos="fade-up">
                    <div class="journey-counter">
                        <h2><span class="home-counter">{{ $generalSettings->reputation_years ?? 0 }}</span>+</h2>
                        <p>Years Reputation</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>




<!-- Feature Section -->
<section class="feature-section-two">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 aos" data-aos="fade-up">
                <div class="section-heading-two">
                    <h2>Our Products</h2>
                    <p>Discover top-quality products designed just for you.</p>
                </div>
            </div>
            <div class="col-md-6 text-md-end aos" data-aos="fade-up">
                <a href="{{route('products')}}" class="btn gradientBTN">View All</a>
            </div>
        </div>
        <div class="row">
            @if(isset($latestProducts) && count($latestProducts) > 0)
                @foreach($latestProducts as $product)
                    <div class="col-md-6 col-lg-4">
                        <div class="feature-widget-main">
                            <div class="feature-widget">
                                <div class="feature-img">
                                    <a href="{{ route('product.details', $product->id) }}">
                                        @if($product->default_img)
                                            <img src="@prodImage($product->default_img)" class="img-fluid" alt="{{ $product->product_name }}">
                                        @else
                                            <img src="@prodImage('assets/img/products/placeholder.jpg')" class="img-fluid" alt="{{ $product->product_name }}">
                                        @endif
                                    </a>
                                </div>
                            </div>
                            <div class="feature-icon">
                                <div class="feature-title">
                                    <h5>{{ $product->product_name }}</h5>
                                    <p>₹{{ number_format($product->offer_price, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-md-6 col-lg-4">
                    <div class="feature-widget-main">
                        <div class="feature-widget">
                            <div class="feature-img">
                                <a href="{{ route('ssd') }}">
                                    <img src="@prodImage('assets/img/products/hhd/main.jpg')" class="img-fluid" alt="img">
                                </a>
                            </div>
                        </div>
                        <div class="feature-icon">
                            <div class="feature-title">
                                <h5>External Hard Disk</h5>
                                <p>₹1800.00</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-widget-main">
                        <div class="feature-widget">
                            <div class="feature-img">
                                <a href="{{ route('monitor') }}">
                                    <img src="@prodImage('assets/img/products/display/main.jpg')" class="img-fluid" alt="img">
                                </a>
                            </div>
                        </div>
                        <div class="feature-icon">
                            <div class="feature-title">
                                <h5>Monitor</h5>
                                <p>₹4900.00</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-widget-main">
                        <div class="feature-widget">
                            <div class="feature-img">
                                <a href="{{ route('airbuds') }}">
                                    <img src="@prodImage('assets/img/products/airbuds/main.jpg')" class="img-fluid" alt="img">
                                </a>
                            </div>
                        </div>
                        <div class="feature-icon">
                            <div class="feature-title">
                                <h5>TWS Earbuds</h5>
                                <p>₹999.00</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-widget-main">
                        <div class="feature-widget">
                            <div class="feature-img">
                                <a href="{{ route('keyboard') }}">
                                    <img src="@prodImage('assets/img/products/keyboard/main.jpg')" class="img-fluid" alt="img">
                                </a>
                            </div>
                        </div>
                        <div class="feature-icon">
                            <div class="feature-title">
                                <h5>Gaming Keyboard</h5>
                                <p>₹1500.00</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-widget-main">
                        <div class="feature-widget">
                            <div class="feature-img">
                                <a href="{{ route('laptop.stand') }}">
                                    <img src="@prodImage('assets/img/products/laptop-stand/main.jpg')" class="img-fluid" alt="img">
                                </a>
                            </div>
                        </div>
                        <div class="feature-icon">
                            <div class="feature-title">
                                <h5>360 Laptop Metal Stand</h5>
                                <p>₹750.00</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-widget-main">
                        <div class="feature-widget">
                            <div class="feature-img">
                                <a href="{{ route('pendrive') }}">
                                    <img src="@prodImage('assets/img/products/pendrive/main.jpg')" class="img-fluid" alt="img">
                                </a>
                            </div>
                        </div>
                        <div class="feature-icon">
                            <div class="feature-title">
                                <h5>Flash Drive</h5>
                                <p>₹400.00</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
<!-- /Feature Section -->



<!-- Client Section -->
<section class="client-section-two">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <div class="section-heading sec-header aos" data-aos="fade-up">
                    <h2>Client Reviews</h2>
                    <p>Description highlights the value of client feedback, showcases genuine client reviews.</p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            @if(isset($generalSettings) && $generalSettings->first_reviewer_name && $generalSettings->first_reviewer_msg)
                <div class="col-lg-4 col-md-6 d-flex">
                    <div class="client-box w-100 aos" data-aos="fade-up">
                        <div class="client-content">
                            <div class="rating">
                                <i class="fas fa-star filled"></i>
                                <i class="fas fa-star filled"></i>
                                <i class="fas fa-star filled"></i>
                                <i class="fas fa-star filled"></i>
                                <i class="fas fa-star filled"></i>
                            </div>
                            <h6>"{{ $generalSettings->first_reviewer_name }}"</h6>
                            <p>{{ $generalSettings->first_reviewer_msg }}</p>
                        </div>
                    </div>
                </div>
            @endif
            @if(isset($generalSettings) && $generalSettings->second_reviewer_name && $generalSettings->second_reviewer_msg)
                <div class="col-lg-4 col-md-6 d-flex">
                    <div class="client-box w-100 aos" data-aos="fade-up">
                        <div class="client-content">
                            <div class="rating">
                                <i class="fas fa-star filled"></i>
                                <i class="fas fa-star filled"></i>
                                <i class="fas fa-star filled"></i>
                                <i class="fas fa-star filled"></i>
                                <i class="fas fa-star filled"></i>
                            </div>
                            <h6>"{{ $generalSettings->second_reviewer_name }}"</h6>
                            <p>{{ $generalSettings->second_reviewer_msg }}</p>
                        </div>
                    </div>
                </div>
            @endif
            @if(isset($generalSettings) && $generalSettings->third_reviewer_name && $generalSettings->third_reviewer_msg)
                <div class="col-lg-4 col-md-6 d-flex">
                    <div class="client-box w-100 aos" data-aos="fade-up">
                        <div class="client-content">
                            <div class="rating">
                                <i class="fas fa-star filled"></i>
                                <i class="fas fa-star filled"></i>
                                <i class="fas fa-star filled"></i>
                                <i class="fas fa-star filled"></i>
                                <i class="fas fa-star filled"></i>
                            </div>
                            <h6>"{{ $generalSettings->third_reviewer_name }}"</h6>
                            <p>{{ $generalSettings->third_reviewer_msg }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
<!-- /Client Section -->


<!-- Cursor -->
<div class="mouse-cursor cursor-outer"></div>
<div class="mouse-cursor cursor-inner"></div>



<!-- /Cursor -->
    @component('components.scrollToTop')
    @endcomponent
@endsection
