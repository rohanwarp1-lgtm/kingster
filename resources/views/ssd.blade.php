<?php $page = 'kingster-hard-disk'; ?>
@extends('layout.mainlayout')
@section('content')

    @component('components.breadcrumb')
        @slot('title')
			Enternal Hard Disk
        @endslot
        @slot('li_1')
            /
        @endslot
        @slot('li_2')
			Enternal Hard Disk
        @endslot
    @endcomponent

	<style>
		span.font-size-14 {
			font-size: 14px;
			font-weight: 600;
		}
	</style>
    <div class="page-wrapper">
		<div class="content">
			<div class="container">
				<div class="row">
					<div class="col-xl-8">
						<div class="card border-0">
							<div class="card-body">
								<div class="service-head mb-2">
									<div class="d-flex align-items-center justify-content-between flex-wrap">
										<h3 class="mb-2">External Hard Disk <span class="font-size-14"><i class="ti ti-star-filled text-warning me-2"></i><span class="text-gray-9">4.9</span>(255 reviews)</span></h3>
										<span class="badge badge-purple-transparent mb-2"><i class="ti ti-calendar-check me-1"></i>6000+ Sold</span>
									</div>
								</div>

								<!-- Slider -->
								<div class="service-wrap mb-4">
									<div class="slider-wrap">
										<div class="owl-carousel service-carousel nav-center mb-3" id="large-img">
											<div class="service-img"><img src="{{ URL::asset('/assets/img/products/hhd/1.jpg') }}" class="img-fluid" alt="Slider Img"></div>
											<div class="service-img"><img src="{{ URL::asset('/assets/img/products/hhd/2.jpg') }}" class="img-fluid" alt="Slider Img"></div>
											<div class="service-img"><img src="{{ URL::asset('/assets/img/products/hhd/3.jpg') }}" class="img-fluid" alt="Slider Img"></div>
											<div class="service-img"><img src="{{ URL::asset('/assets/img/products/hhd/4.jpg') }}" class="img-fluid" alt="Slider Img"></div>
										</div>
										<a href="{{URL::asset('assets/img/services/service-slider-01.jpg')}}" data-fancybox="gallery" class="btn btn-white btn-sm view-btn d-none"><i class="feather-image me-1"></i>View all 20 Images</a>
									</div>
									<div class="owl-carousel slider-nav-thumbnails nav-center d-none" id="small-img">
										<div><img src="{{ URL::asset('/assets/img/products/hhd/1.jpg') }}" class="img-fluid" alt="Slider Img"></div>
										<div><img src="{{ URL::asset('/assets/img/products/hhd/2.jpg') }}" class="img-fluid" alt="Slider Img"></div>
										<div><img src="{{ URL::asset('/assets/img/products/hhd/3.jpg') }}" class="img-fluid" alt="Slider Img"></div>
										<div><img src="{{ URL::asset('/assets/img/products/hhd/4.jpg') }}" class="img-fluid" alt="Slider Img"></div>
									</div>
								</div>
								<!-- /Slider -->

								<div class="accordion service-accordion">
									<div class="accordion-item mb-4">
										<h2 class="mb-2">Service Overview</h2>
										<div class="px-2">
											<p>At Kingster, we are committed to providing premium storage solutions backed by reliable after-sales support. When you purchase a Kingster External Hard Drive, you're not just getting high-speed data storage—you’re gaining peace of mind with our dedicated customer service.</p>
											<p>Warranty Period: 1-Year Limited Warranty from the date of purchase.</p>
											<p>Coverage: Manufacturing defects, drive malfunctions, and cable issues under normal usage conditions.</p>
											<p>Exclusions: Physical damage, water exposure, and data loss not caused by the hardware.</p>
											<p>How to Claim Warranty Keep your original purchase invoice as proof.</p>
											<p>Contact our support team via Email or WhatsApp with:</p>
											<p>Product Name & Serial Number, Purchase Invoice, Description or video of the issue</p>
											<p>📞 Customer Support</p>
											<p>Email / number : support@kingster.info</p>
											<p>Call ☎️ : +1800 890 5576</p>
											<p>Working Hours: Monday to Saturday, 09:00 AM – 09:00 PM</p>
										</div>
									</div>
									<div class="accordion-item mb-4">
										<h2 class="accordion-header">Includes</h2>
										<div class="bg-light-200 p-3 pb-2 br-10">
											<p class="d-inline-flex align-items-center mb-2 me-4"><i class="feather-check-circle text-success me-2"></i><strong>type-c port</strong></p>
											<p class="d-inline-flex align-items-center mb-2 me-4"><i class="feather-check-circle text-success me-2"></i><strong>1 x Kingster Ultra Slim External Hard Drive</strong></p>
											<p class="d-inline-flex align-items-center mb-2 me-4"><i class="feather-check-circle text-success me-2"></i><strong>1 x High-Speed USB 3.0 Data Cable</strong></p>
											<p class="d-inline-flex align-items-center mb-2 me-4"><i class="feather-check-circle text-success me-2"></i><strong>1 x User Guide / Quick Start Manual</strong></p>
											<p class="d-inline-flex align-items-center mb-2 me-4"><i class="feather-check-circle text-success me-2"></i><strong>1 x Warranty Card</strong></p>
											<p class="d-inline-flex align-items-center mb-2 me-4"><i class="feather-check-circle text-success me-2"></i><strong>Protective Bubble Packing (for safe transit)</strong></p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-xl-4 theiaStickySidebar">
						<div class="card border-0">
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between border-bottom mb-3">
									<div class="d-flex align-items-center">
										<div class="mb-3">
											<p class="fs-14 mb-0">Starts From</p>
											<h4><span class="display-6 fw-bold" id="selling-pricing">₹1800</span><span class="text-decoration-line-through text-default" id="offer-pricing"> ₹2000</span></h4>
										</div>
									</div>
									<span class="badge bg-success mb-3 d-inline-flex align-items-center fw-medium"><i class="ti ti-circle-percentage me-1"></i><span id="offer-discount">10</span>% Offer</span>
								</div>

								<div class="d-flex align-items-center justify-content-between mb-3">
									<button type="button" class="w-100 btn btn-primary me-2 variant-btn" data-price="1800" data-oprice="2000" id="threeTwoGB">320GB</button>
									<button type="button" class="w-100 btn btn-outline-primary me-2 variant-btn" data-price="2200" data-oprice="2500" id="fiveZeroGB">500GB</button>
									<button type="button" class="w-100 btn btn-outline-primary variant-btn" data-price="3500" data-oprice="4000" id="oneTB">1TB</button>
								</div>

								<a href="https://api.whatsapp.com/send?phone=918866513744&text=Hello Kingster Team!!" target="_blank"  class="btn btn-lg btn-outline-light d-flex align-items-center justify-content-center w-100"><i class="ti ti-brand-whatsapp me-2"></i>Send Enquiry</a>
							</div>
						</div>

						<div class="card border-0">
							<div class="card-body">
								<h4 class="mb-3">Business Hours</h4>
								<div class="d-flex align-items-center justify-content-between mb-3">
									<h6 class="fs-16 fw-medium mb-0">Monday</h6>
									<p>9:00 AM - 9:00 PM</p>
								</div>
								<div class="d-flex align-items-center justify-content-between mb-3">
									<h6 class="fs-16 fw-medium mb-0">Tuesday</h6>
									<p>9:00 AM - 9:00 PM</p>
								</div>
								<div class="d-flex align-items-center justify-content-between mb-3">
									<h6 class="fs-16 fw-medium mb-0">Wednesday</h6>
									<p>9:00 AM - 9:00 PM</p>
								</div>
								<div class="d-flex align-items-center justify-content-between mb-3">
									<h6 class="fs-16 fw-medium mb-0">Thursday</h6>
									<p>9:00 AM - 9:00 PM</p>
								</div>
								<div class="d-flex align-items-center justify-content-between mb-3">
									<h6 class="fs-16 fw-medium mb-0">Friday</h6>
									<p>9:00 AM - 9:00 PM</p>
								</div>
								<div class="d-flex align-items-center justify-content-between mb-3">
									<h6 class="fs-16 fw-medium mb-0">Saturday</h6>
									<p>9:00 AM - 9:00 PM</p>
								</div>
								<div class="d-flex align-items-center justify-content-between mb-0">
									<h6 class="fs-16 fw-medium mb-0">Sunday</h6>
									<p>9:00 AM - 9:00 PM</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
    @component('components.model-popup')
    @endcomponent
    @component('components.scrollToTop')
     @endcomponent

@endsection

@section('productfile-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function updatePricing(price, oprice) {
        document.getElementById('selling-pricing').textContent = '₹' + parseFloat(price).toLocaleString('en-IN', {minimumFractionDigits: 2});
        document.getElementById('offer-pricing').textContent = ' ₹' + parseFloat(oprice).toLocaleString('en-IN', {minimumFractionDigits: 2});
        document.getElementById('offer-discount').textContent = Math.round(((oprice - price) / oprice) * 100);
    }
    document.querySelectorAll('.variant-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.variant-btn').forEach(function(b) {
                b.classList.remove('btn-primary');
                b.classList.add('btn-outline-primary');
            });
            this.classList.remove('btn-outline-primary');
            this.classList.add('btn-primary');
            updatePricing(this.dataset.price, this.dataset.oprice);
        });
    });
});
</script>
@endsection
