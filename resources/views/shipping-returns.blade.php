<?php $page = 'privacy-policy'; ?>
@extends('layout.mainlayout')
@section('content')
   
    @component('components.breadcrumb')
        @slot('title')
            Shipping & Returns
        @endslot
        @slot('li_1')
            /
        @endslot
        @slot('li_2')
            Shipping & Returns
        @endslot
    @endcomponent

<style>
    hr{
        border-color: #203066 !important;
    }
</style>
<div class="page-wrapper">
    <div class="content">
        <div class="container">
            <div class="row">
            
                <div class="col-md-12">
                    <div class="terms-content privacy-cont">
                        @if(!empty($generalSettings?->shipping_returns_content))
                            {!! $generalSettings->shipping_returns_content !!}
                        @else
                        <h4 class="mb-4">Shipping, Returns & Refunds</h4>

                        <h6>Is Free shipping available?</h6>
                        <p>Yes, our prices are transparent and include free shipping, packaging and taxes unless specified otherwise.</p>
                        <hr>
                        
                        <h6>How long does it take to deliver my order?</h6>
                        <p>Orders are typically processed within 1-3 business days of placement. Once you receive a shipping confirmation, your order should be delivered within 3-7 business days, depending on the delivery location. Some locations may experience longer durations based on delivery location and logistics. If your order is urgent, contact us at {{ isset($generalSettings) && $generalSettings->customer_support_email ? $generalSettings->customer_support_email : 'support@kingster.info' }}, and we'll do our best to assist.</p>
                        <hr>

                        <h6>Where do you ship?</h6>
                        <p>We provide free shipping across India and may have international shipping soon. Meanwhile, we can ship internationally for an additional fee, on a case-by-case basis.</p>
                        <hr>

                        <h5>RETURNS</h5>
                        <hr>

                        <h6>What is your return policy?</h6>
                        <p>All products sold on our website will be delivered in pristine condition. Products carry warranty as respectively mentioned and service can be obtained from service centres nearest to your location. In case of difficulties, please contact us at {{ isset($generalSettings) && $generalSettings->customer_support_email ? $generalSettings->customer_support_email : 'support@kingster.info' }}.</p>
                        <hr>

                        <h6>What if my product does not work upon delivery/or is Dead on Arrival(DOA)?</h6>
                        <p>In the unlikely case that your KINGSTER product is not working upon delivery, please email high-resolution video along with original packaging and images to {{ isset($generalSettings) && $generalSettings->customer_support_email ? $generalSettings->customer_support_email : 'support@kingster.info' }} within 24 hours of your order being delivered.</p>
                        <hr>

                        <h6>What if my product arrived is damaged/wrong/have accessories missing?</h6>
                        <p>For damaged/wrong/missing accessories on delivery, please email high-resolution video along with original packaging and images to {{ isset($generalSettings) && $generalSettings->customer_support_email ? $generalSettings->customer_support_email : 'support@kingster.info' }} within 24 hours of your order being delivered.</p>
                        <hr>

                        <h5>CANCELLATION</h5>
                        <h6>How to cancel my order?</h6>
                        <p>The order can be cancelled only if it has not yet been dispatched. To cancel your order before it has been dispatched, please call on {{ isset($generalSettings) && $generalSettings->customer_support_mobile ? $generalSettings->customer_support_mobile : '+91 88665 13744' }}.</p>
                        <hr>
                        
                        <h5>REFUNDS</h5>
                        <p>We do not offer returns on sold products. We strive for an outstanding shopping experience with full disclosure and transparency. Refunds are considered on a case-by-case basis. If approved, the refund will be processed within 10 business days to your original payment method. For delays beyond 15 business days, please contact us at {{ isset($generalSettings) && $generalSettings->customer_support_email ? $generalSettings->customer_support_email : 'support@kingster.info' }}.</p>
                        <hr>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
