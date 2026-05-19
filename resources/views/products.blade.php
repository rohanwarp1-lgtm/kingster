<?php $page = 'products'; ?>
@extends('layout.mainlayout')
@section('content')

	@component('components.breadcrumb')
        @slot('title')
			Our Products
        @endslot
        @slot('li_1')
            /
        @endslot
        @slot('li_2')
		Our Products
        @endslot
    @endcomponent

	<style>
		span.font-size-14 {
			font-size: 14px;
			font-weight: 600;
		}
	</style>

	@php
		$fallbackProducts = [
			[
				'name' => 'External Hard Disk',
				'price' => 1800,
				'image' => 'assets/img/products/hhd/main.jpg',
				'url' => route('ssd'),
			],
			[
				'name' => 'Monitor',
				'price' => 4900,
				'image' => 'assets/img/products/display/main.jpg',
				'url' => route('monitor'),
			],
			[
				'name' => 'TWS Earbuds',
				'price' => 999,
				'image' => 'assets/img/products/airbuds/main.jpg',
				'url' => route('airbuds'),
			],
			[
				'name' => 'Gaming Keyboard',
				'price' => 1500,
				'image' => 'assets/img/products/keyboard/main.jpg',
				'url' => route('keyboard'),
			],
			[
				'name' => '360 Laptop Metal Stand',
				'price' => 750,
				'image' => 'assets/img/products/laptop-stand/main.jpg',
				'url' => route('laptop.stand'),
			],
			[
				'name' => 'Flash Drive',
				'price' => 400,
				'image' => 'assets/img/products/pendrive/main.jpg',
				'url' => route('pendrive'),
			],
		];
	@endphp

    <div class="page-wrapper">
		<div class="content">
			<div class="container">
				<!-- Feature Section -->
				<section class="feature-section-two pt-0">
					<div class="container">
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
								@foreach($fallbackProducts as $product)
								<div class="col-md-6 col-lg-4">
									<div class="feature-widget-main">
										<div class="feature-widget">
											<div class="feature-img">
												<a href="{{ $product['url'] }}">
													<img src="@prodImage($product['image'])" class="img-fluid" alt="{{ $product['name'] }}">
												</a>
											</div>
										</div>
										<div class="feature-icon">
											<div class="feature-title">
												<h5>{{ $product['name'] }}</h5>
												<p>₹{{ number_format($product['price'], 2) }}</p>
											</div>
										</div>
									</div>
								</div>
								@endforeach
							@endif
						</div>
					</div>
				</section>
				<!-- /Feature Section -->
			</div>
		</div>
	</div>
    @component('components.model-popup')
    @endcomponent
    @component('components.scrollToTop')
     @endcomponent

@endsection
