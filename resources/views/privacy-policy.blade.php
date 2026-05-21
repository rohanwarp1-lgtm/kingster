<?php $page = 'privacy-policy'; ?>
@extends('layout.mainlayout')
@section('content')

    @component('components.breadcrumb')
        @slot('title')
            Privacy Policy
        @endslot
        @slot('li_1')
            /
        @endslot
        @slot('li_2')
            Privacy Policy
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
                        @if(!empty($generalSettings?->privacy_policy_content))
                            {!! $generalSettings->privacy_policy_content !!}
                        @else
                        <p>Kingster operates the kingster.in website.</p>
                        <p>This page is used to inform website visitors regarding our policies with the collection, use, and disclosure of Personal Information for anyone who chooses to use our Service via the kingster.in website.</p>
                        <p>By using our Service, you agree to the collection and use of information in accordance with this policy. The Personal Information that we collect is used for providing and improving the Service. We do not use or share your information with anyone except as described in this Privacy Policy.</p>
                        <p>The terms used in this Privacy Policy have the same meanings as in our Terms and Conditions, which are accessible at www.kingster.info, unless otherwise defined in this Privacy Policy.</p>
                        <hr>

                        <h6 class="mb-2">Information Collection and Use</h6>
                        <p>To provide you with a better experience while using our Service, we may ask you to provide us with certain personally identifiable information, such as your name, phone number, and postal address. The information we collect will be used to contact or identify you.</p>
                        <hr>

                        <h6 class="mb-2">Log Data</h6>
                        <p>We want to inform you that whenever you visit our Service, we collect information that your browser sends to us, called Log Data. This Log Data may include details such as your computer’s Internet Protocol (“IP”) address, browser version, the pages of our Service that you visit, the time and date of your visit, the time spent on those pages, and other statistics.</p>
                        <hr>

                        <h6 class="mb-2">Cookies</h6>
                        <p>Cookies are small files with a small amount of data, commonly used as anonymous unique identifiers. These are sent to your browser from the websites you visit and are stored on your computer's hard drive.</p>
                        <p>Our website uses these “cookies” to collect information and to improve our Service. You have the option to accept or refuse these cookies and to know when a cookie is being sent to your computer. If you choose to refuse our cookies, some portions of our Service may not function properly.</p>
                        <hr>

                        <h6 class="mb-2">Security</h6>
                        <p>We value your trust in providing us your Personal Information, and we strive to use commercially acceptable means of protecting it. However, remember that no method of transmission over the internet or method of electronic storage is 100% secure, and we cannot guarantee absolute security.</p>
                        <hr>

                        <h6 class="mb-2">Changes to This Privacy Policy</h6>
                        <p>We may update our Privacy Policy from time to time. We advise you to review this page periodically for any changes. Any updates will be posted on this page and are effective immediately after posting.</p>
                        <hr>

                        <h6 class="mb-2">Contact Us</h6>
                        <p>If you have any questions or suggestions about our Privacy Policy, feel free to contact us at:</p>
                        <p><a href="#">Email :- {{ isset($generalSettings) && $generalSettings->customer_support_email ? $generalSettings->customer_support_email : 'support@kingster.info' }} || Call On :- {{ isset($generalSettings) && $generalSettings->customer_support_mobile ? $generalSettings->customer_support_mobile : '+91 88665 13744' }}</a></p>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
