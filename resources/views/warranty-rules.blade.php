<?php $page = 'product-replacement-policy'; ?>
@extends('layout.mainlayout')
@section('content')

    @component('components.breadcrumb')
        @slot('title')
            Replacement Policy
        @endslot
        @slot('li_1')
            /
        @endslot
        @slot('li_2')
            Replacement Policy
        @endslot
    @endcomponent

    <style>
        hr{
            border-color: #203066 !important;
        }

        @media (max-width: 768px) {
            .form-section.shadow.p-4 {
                width: 100% !important;
            }
        }

        .card-body{
            padding: 15px 20px !important;
        }
        .card-body p{
            color: #000;
            margin-bottom: 5px;
        }

        .policyPage p{
            margin-bottom: 5px;
        }

        .policyPage h6{
            margin-bottom: 6px;
        }

        .card-body strong{
            font-weight: 600;
            color: black;
        }
        h1, h2 {
            color: #2c3e50;
        }
        .policyPage li ul {
            list-style: unset !important;
        }
        .policyPage ul {
            padding-left: 20px;
            list-style: unset !important;
            margin-left: 10px;
        }
        .policyPage strong {
            color: #34495e;
        }
        .note {
            color: #e74c3c;
        }
    </style>



@php $policySetting = $generalSettings ?? \App\Models\GeneralSetting::first(); @endphp

<div class="page-wrapper">
    <div class="content">
        <div class="container policyPage">

            @if(!empty($policySetting?->replacement_policy_content))
                {!! $policySetting->replacement_policy_content !!}
            @else
                <h3 class="text-center">Kingster Replacement Policy</h3>
                <hr>

                <p>At Kingster, we are committed to providing high-quality tech products and excellent customer service. If you encounter an issue with a product you've purchased from us, we offer a straightforward replacement policy as outlined below.</p>

                <h6>Eligible Products</h6>
                <p>This policy applies to the following product categories purchased directly from Kingster:</p>
                <ul>
                    <li style="font-weight: 600;">External Hard Disk Drives</li>
                    <li style="font-weight: 600;">Laptop Internal Hard Disk Drives</li>
                    <li style="font-weight: 600;">Security Cameras</li>
                    <li style="font-weight: 600;">RAM Modules</li>
                    <li style="font-weight: 600;">Processors</li>
                    <li style="font-weight: 600;">Other electronic accessories as listed on our website</li>
                </ul>

                <hr>

                <h6>Replacement Conditions</h6>
                <p>To qualify for a replacement:</p>
                <ul>
                    <li style="font-weight: 600;">The product must be within the warranty period.</li>
                    <li style="font-weight: 600;">The defect must not be caused by misuse, physical damage, improper installation, or unauthorized modification.</li>
                    <li style="font-weight: 600;">The product serial number must be intact and match our records.</li>
                </ul>

                <hr>

                <h6>Replacement Process</h6>
                <ol style="list-style: none !important;">
                    <li>
                        <strong>Initiate a Request:</strong><br>
                        Contact our customer support via <em>[support@kingster.info or +91 88665 13744]</em> and provide:
                        <ul style="list-style: dot !important;">
                            <li style="font-weight: 500;">Order details</li>
                            <li style="font-weight: 500;">Description of the issue</li>
                            <li style="font-weight: 500;">Photos or videos (if applicable)</li>
                        </ul>
                    </li>
                    <li>
                        <strong>Ship the Product to Us: </strong><span>Once approved, send the product to our service center at your own shipping cost. Ensure secure packaging to avoid damage.</span>
                    </li>
                    <li>
                        <strong>Inspection & Approval: </strong>
                        <span>Our technical team will inspect and verify the issue. If approved, we will dispatch a replacement within 3–5 business days.</span>
                    </li>
                    <li>
                        <strong>Replacement Shipment: </strong>
                        <span>We will cover the shipping cost for the replacement product.</span>
                    </li>
                </ol>

                <hr>

                <h6>Important Notes</h6>
                <ul>
                    <li style="font-weight: 600;">Replacement is subject to stock availability.</li>
                    <li style="font-weight: 600;">If a replacement is not available, a product of equal value or a refund may be offered at company discretion.</li>
                    <li><span class="note" style="font-weight: 500;">Products sent without prior communication or approval will not be accepted.</span></li>
                </ul>

                <hr>

                <h6>Non-Replacementable Items</h6>
                <ul>
                    <li style="font-weight: 600;">Opened or used products (except for defective items)</li>
                    <li style="font-weight: 600;">Items missing original packaging, manuals, or accessories</li>
                    <li style="font-weight: 600;">Items with missing serial numbers, labels, or tampered warranties</li>
                    <li style="font-weight: 600;">Software, licenses, or digital downloads</li>
                    <li style="font-weight: 600;">Products damaged by misuse, mishandling, or modification</li>
                </ul>

                <hr>

                <h6>Contact Us</h6>
                <p>For any questions or support, please contact:</p>
                <ul>
                    <li><strong>Email:</strong> support@kingster.info</li>
                    <li><strong>Phone:</strong> +91 88665 13744</li>
                </ul>
            @endif

        </div>
    </div>
</div>



@endsection
