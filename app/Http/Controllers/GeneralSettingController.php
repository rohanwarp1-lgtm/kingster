<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use Session;
use App\Models\Warranty;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\GeneralSetting;


class GeneralSettingController extends Controller
{
    public function index(){
        $generalSettings = GeneralSetting::first() ?? new GeneralSetting();
        return view('admin/general_setting', compact('generalSettings'));
    }

    public function save(Request $request){
        $validated = $request->validate([
            'brand_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'brand_white_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'brand_fevicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:2048',
            'header_banner_1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'header_banner_2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'header_banner_3' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'header_banner_4' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'customer_support_mobile' => 'nullable|string|max:255',
            'customer_support_email' => 'nullable|email|max:255',
            'office_time' => 'nullable|string|max:255',
            'footer_about_us_1' => 'nullable|string',
            'footer_about_us_2' => 'nullable|string',
            'ig_link' => 'nullable|string|max:255',
            'wp_link' => 'nullable|string|max:255',
            'footer_copyright' => 'nullable|string|max:255',
            'active_clients' => 'nullable|integer',
            'expert_mechanics' => 'nullable|integer',
            'reputation_years' => 'nullable|integer',
            'first_reviewer_name' => 'nullable|string|max:255',
            'first_reviewer_msg' => 'nullable|string',
            'second_reviewer_name' => 'nullable|string|max:255',
            'second_reviewer_msg' => 'nullable|string',
            'third_reviewer_name' => 'nullable|string|max:255',
            'third_reviewer_msg' => 'nullable|string',
        ]);

        $setting = GeneralSetting::first() ?? new GeneralSetting();

        $fieldsWithFiles = [
            'brand_logo', 'brand_white_logo', 'brand_fevicon',
            'header_banner_1', 'header_banner_2',
            'header_banner_3', 'header_banner_4'
        ];
        foreach ($fieldsWithFiles as $field) {
            if ($request->hasFile($field)) {
                $image = $request->file($field);
                $uniqueName = time() . '_' . mt_rand(100000, 999999);
                $ext = strtolower($image->getClientOriginalExtension());
                $imageName = $uniqueName . '_general_setting.' . $ext;
                $image->move(base_path('public/uploads/general_settings'), $imageName);
                $setting->$field = 'uploads/general_settings/' . $imageName;
            }
        }

        $setting->customer_support_mobile = $request->customer_support_mobile;
        $setting->customer_support_email = $request->customer_support_email;
        $setting->office_time = $request->office_time;
        $setting->footer_about_us_1 = $request->footer_about_us_1;
        $setting->footer_about_us_2 = $request->footer_about_us_2;
        $setting->ig_link = $request->ig_link;
        $setting->wp_link = $request->wp_link;
        $setting->footer_copyright = $request->footer_copyright;
        $setting->active_clients = isset($request->active_clients) ? $request->active_clients : 212089;
        $setting->expert_mechanics = isset($request->expert_mechanics) ? $request->expert_mechanics : 10;
        $setting->reputation_years = isset($request->reputation_years) ? $request->reputation_years : 7;
        $setting->first_reviewer_name = $request->first_reviewer_name;
        $setting->first_reviewer_msg = $request->first_reviewer_msg;
        $setting->second_reviewer_name = $request->second_reviewer_name;
        $setting->second_reviewer_msg = $request->second_reviewer_msg;
        $setting->third_reviewer_name = $request->third_reviewer_name;
        $setting->third_reviewer_msg = $request->third_reviewer_msg;

        $setting->modified_by = auth()->id() ?? 1;

        $setting->save();

        return response()->json(['success' => true, 'message' => 'Settings saved successfully!']);
    }

    public function replacementPolicy(Request $request)
    {
        $setting = GeneralSetting::first() ?? new GeneralSetting();
        $policyPages = $this->policyPageDefinitions($setting);
        $activePage = $request->get('page', 'replacement');

        if (! array_key_exists($activePage, $policyPages)) {
            $activePage = 'replacement';
        }

        return view('admin.replacement_policy', compact('setting', 'policyPages', 'activePage'));
    }

    public function saveReplacementPolicy(Request $request)
    {
        $pageConfig = $this->policyPageConfig();

        $request->validate([
            'page' => 'required|string|in:' . implode(',', array_keys($pageConfig)),
            'page_content' => 'required|string',
        ]);

        $page = $request->input('page');
        $field = $pageConfig[$page]['field'];

        $setting = GeneralSetting::first() ?? new GeneralSetting();
        $setting->{$field} = $request->page_content;
        $setting->modified_by = auth()->id() ?? 1;
        $setting->save();

        return response()->json([
            'success' => true,
            'message' => $pageConfig[$page]['label'] . ' saved successfully!',
        ]);
    }

    private function policyPageConfig(): array
    {
        return [
            'replacement' => [
                'label' => 'Replacement Policy',
                'field' => 'replacement_policy_content',
                'route' => 'warranty.replacement.policy',
            ],
            'privacy' => [
                'label' => 'Privacy Policy',
                'field' => 'privacy_policy_content',
                'route' => 'privacy-policy',
            ],
            'terms' => [
                'label' => 'Terms & Condition',
                'field' => 'terms_condition_content',
                'route' => 'terms-condition',
            ],
            'shipping' => [
                'label' => 'Shipping & Returns',
                'field' => 'shipping_returns_content',
                'route' => 'shipping-returns',
            ],
            'warranty_declaration' => [
                'label' => 'Warranty Declaration (Application Form)',
                'field' => 'warranty_declaration_text',
                'route' => 'warranty.apply.view',
            ],
        ];
    }

    private function policyPageDefinitions(GeneralSetting $setting): array
    {
        $defaults = $this->defaultPolicyPageContents($setting);
        $pages = [];

        foreach ($this->policyPageConfig() as $key => $config) {
            $content = $setting->{$config['field']} ?? null;
            $pages[$key] = [
                'label' => $config['label'],
                'field' => $config['field'],
                'previewUrl' => route($config['route']),
                'content' => $content ?: ($defaults[$key] ?? ''),
                'isCustom' => ! empty($content),
            ];
        }

        return $pages;
    }

    private function defaultPolicyPageContents(GeneralSetting $setting): array
    {
        $email = $setting->customer_support_email ?: 'support@kingster.info';
        $mobile = $setting->customer_support_mobile ?: '+91 88665 13744';

        return [
            'replacement' => <<<HTML
<h3 class="text-center">Kingster Replacement Policy</h3>
<hr>
<p>At Kingster, we are committed to providing high-quality tech products and excellent customer service. If you encounter an issue with a product you've purchased from us, we offer a straightforward replacement policy as outlined below.</p>
<h6>Eligible Products</h6>
<p>This policy applies to the following product categories purchased directly from Kingster:</p>
<ul>
    <li><strong>External Hard Disk Drives</strong></li>
    <li><strong>Laptop Internal Hard Disk Drives</strong></li>
    <li><strong>Security Cameras</strong></li>
    <li><strong>RAM Modules</strong></li>
    <li><strong>Processors</strong></li>
    <li><strong>Other electronic accessories as listed on our website</strong></li>
</ul>
<hr>
<h6>Replacement Conditions</h6>
<p>To qualify for a replacement:</p>
<ul>
    <li><strong>The product must be within the warranty period.</strong></li>
    <li><strong>The defect must not be caused by misuse, physical damage, improper installation, or unauthorized modification.</strong></li>
    <li><strong>The product serial number must be intact and match our records.</strong></li>
</ul>
<hr>
<h6>Replacement Process</h6>
<ol>
    <li><strong>Initiate a Request:</strong><br>Contact our customer support via <em>{$email} or {$mobile}</em> and provide order details, a description of the issue, and photos or videos if applicable.</li>
    <li><strong>Ship the Product to Us:</strong> Once approved, send the product to our service center at your own shipping cost. Ensure secure packaging to avoid damage.</li>
    <li><strong>Inspection & Approval:</strong> Our technical team will inspect and verify the issue. If approved, we will dispatch a replacement within 3-5 business days.</li>
    <li><strong>Replacement Shipment:</strong> We will cover the shipping cost for the replacement product.</li>
</ol>
<hr>
<h6>Important Notes</h6>
<ul>
    <li><strong>Replacement is subject to stock availability.</strong></li>
    <li><strong>If a replacement is not available, a product of equal value or a refund may be offered at company discretion.</strong></li>
    <li><span class="note"><strong>Products sent without prior communication or approval will not be accepted.</strong></span></li>
</ul>
<hr>
<h6>Non-Replacementable Items</h6>
<ul>
    <li><strong>Opened or used products, except for defective items</strong></li>
    <li><strong>Items missing original packaging, manuals, or accessories</strong></li>
    <li><strong>Items with missing serial numbers, labels, or tampered warranties</strong></li>
    <li><strong>Software, licenses, or digital downloads</strong></li>
    <li><strong>Products damaged by misuse, mishandling, or modification</strong></li>
</ul>
<hr>
<h6>Contact Us</h6>
<p>For support, contact <strong>{$email}</strong> or <strong>{$mobile}</strong>.</p>
HTML,
            'privacy' => <<<HTML
<p>Kingster operates the kingster.in website.</p>
<p>This page is used to inform website visitors regarding our policies with the collection, use, and disclosure of Personal Information for anyone who chooses to use our Service via the kingster.in website.</p>
<p>By using our Service, you agree to the collection and use of information in accordance with this policy. The Personal Information that we collect is used for providing and improving the Service. We do not use or share your information with anyone except as described in this Privacy Policy.</p>
<p>The terms used in this Privacy Policy have the same meanings as in our Terms and Conditions, which are accessible at www.kingster.info, unless otherwise defined in this Privacy Policy.</p>
<hr>
<h6 class="mb-2">Information Collection and Use</h6>
<p>To provide you with a better experience while using our Service, we may ask you to provide us with certain personally identifiable information, such as your name, phone number, and postal address. The information we collect will be used to contact or identify you.</p>
<hr>
<h6 class="mb-2">Log Data</h6>
<p>We want to inform you that whenever you visit our Service, we collect information that your browser sends to us, called Log Data. This Log Data may include details such as your computer's Internet Protocol address, browser version, the pages of our Service that you visit, the time and date of your visit, the time spent on those pages, and other statistics.</p>
<hr>
<h6 class="mb-2">Cookies</h6>
<p>Cookies are small files with a small amount of data, commonly used as anonymous unique identifiers. These are sent to your browser from the websites you visit and are stored on your computer's hard drive.</p>
<p>Our website uses these cookies to collect information and to improve our Service. You have the option to accept or refuse these cookies and to know when a cookie is being sent to your computer. If you choose to refuse our cookies, some portions of our Service may not function properly.</p>
<hr>
<h6 class="mb-2">Security</h6>
<p>We value your trust in providing us your Personal Information, and we strive to use commercially acceptable means of protecting it. However, remember that no method of transmission over the internet or method of electronic storage is 100% secure, and we cannot guarantee absolute security.</p>
<hr>
<h6 class="mb-2">Changes to This Privacy Policy</h6>
<p>We may update our Privacy Policy from time to time. We advise you to review this page periodically for any changes. Any updates will be posted on this page and are effective immediately after posting.</p>
<hr>
<h6 class="mb-2">Contact Us</h6>
<p>If you have any questions or suggestions about our Privacy Policy, feel free to contact us at:</p>
<p><a href="mailto:{$email}">Email :- {$email} || Call On :- {$mobile}</a></p>
HTML,
            'terms' => <<<HTML
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
<p>Most products come with a standard limited warranty. Returns and replacements are accepted as per our return policy, provided the item is in unused condition and returned within the specified time frame.</p>
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
HTML,
            'shipping' => <<<HTML
<h4 class="mb-4">Shipping, Returns & Refunds</h4>
<h6>Is Free shipping available?</h6>
<p>Yes, our prices are transparent and include free shipping, packaging and taxes unless specified otherwise.</p>
<hr>
<h6>How long does it take to deliver my order?</h6>
<p>Orders are typically processed within 1-3 business days of placement. Once you receive a shipping confirmation, your order should be delivered within 3-7 business days, depending on the delivery location. Some locations may experience longer durations based on delivery location and logistics. If your order is urgent, contact us at {$email}, and we'll do our best to assist.</p>
<hr>
<h6>Where do you ship?</h6>
<p>We provide free shipping across India and may have international shipping soon. Meanwhile, we can ship internationally for an additional fee, on a case-by-case basis.</p>
<hr>
<h5>RETURNS</h5>
<hr>
<h6>What is your return policy?</h6>
<p>All products sold on our website will be delivered in pristine condition. Products carry warranty as respectively mentioned and service can be obtained from service centres nearest to your location. In case of difficulties, please contact us at {$email}.</p>
<hr>
<h6>What if my product does not work upon delivery/or is Dead on Arrival(DOA)?</h6>
<p>In the unlikely case that your KINGSTER product is not working upon delivery, please email high-resolution video along with original packaging and images to {$email} within 24 hours of your order being delivered.</p>
<hr>
<h6>What if my product arrived is damaged/wrong/have accessories missing?</h6>
<p>For damaged/wrong/missing accessories on delivery, please email high-resolution video along with original packaging and images to {$email} within 24 hours of your order being delivered.</p>
<hr>
<h5>CANCELLATION</h5>
<h6>How to cancel my order?</h6>
<p>The order can be cancelled only if it has not yet been dispatched. To cancel your order before it has been dispatched, please call on {$mobile}.</p>
<hr>
<h5>REFUNDS</h5>
<p>We do not offer returns on sold products. We strive for an outstanding shopping experience with full disclosure and transparency. Refunds are considered on a case-by-case basis. If approved, the refund will be processed within 10 business days to your original payment method. For delays beyond 15 business days, please contact us at {$email}.</p>
<hr>
HTML,
        ];
    }

}
