<?php $page = 'terms-condition'; ?>
@extends('layout.mainlayout')
@section('content')
   
    @component('components.breadcrumb')
        @slot('title')
            Terms & Condition
        @endslot
        @slot('li_1')
            /
        @endslot
        @slot('li_2')
            Terms & Condition
        @endslot
    @endcomponent

   <!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">
				<div class="container">
					<div class="row">
					
						<!-- Terms & Conditions -->
						<div class="col-md-12">
							<div class="terms-content privacy-cont">
								@if(!empty($generalSettings?->terms_condition_content))
									{!! $generalSettings->terms_condition_content !!}
								@else
								<h6>1. General</h6>
								<p>Kingster is a tech brand offering a range of computer accessories and storage devices. By purchasing our products or using our services, you agree to comply with and be bound by the following terms and conditions.</p>
								<hr>
								<h6>2. Product Availability</h6>
								<p>All products listed are subject to availability. We reserve the right to modify, discontinue, or limit the quantities of any product at any time without prior notice.</p>
								<hr>
								<h6>3. Pricing & Payments</h6>
								<p>Prices are listed in INR and include all applicable taxes unless stated otherwise. We reserve the right to change pricing at any time. All payments must be completed using the available payment methods.</p>
								<hr>
								<h6>4. Warranty & Returns</h6>
								<p>Most products come with a standard limited warranty (details available on individual product packaging or our website). Returns and replacements are accepted as per our return policy, provided the item is in unused condition and returned within the specified time frame.</p>
								<hr>
								<h6>5. Shipping & Delivery</h6>
								<p>Shipping timelines may vary depending on the delivery location. While we strive to ensure timely delivery, we are not liable for delays caused by external factors beyond our control.</p>
								<hr>
								<h6>6. Limitation of Liability</h6>
								<p>Kingster shall not be liable for any indirect, incidental, or consequential damages arising from the use of our products or services. All products should be used as per user manuals and safety guidelines.</p>
								<hr>
								<h6>7. Support Services</h6>
								<p>We offer 24x7 customer support. You can reach us through our official channels for any queries, complaints, or warranty-related issues.</p>
								<hr>
								<h6>8. Intellectual Property</h6>
								<p>All content, logos, product designs, and trademarks are the property of Kingster and are protected by applicable intellectual property laws. Unauthorized use is strictly prohibited.</p>
								<hr>
								<h6>9. User Conduct</h6>
								<p>You agree not to misuse our services, attempt to gain unauthorized access to systems, or use our platform for fraudulent or harmful activities.</p>
								<hr>
								<h6>10. Changes to Terms</h6>
								<p>We may revise these Terms from time to time. Continued use of our services after any such changes shall constitute your consent to the revised terms.</p>
								<hr>
								@endif
							</div>
						</div>
						<!-- /Terms & Conditions -->
						
					</div>
				</div>
			</div>
		</div>
		<!-- /Page Wrapper -->
@endsection
