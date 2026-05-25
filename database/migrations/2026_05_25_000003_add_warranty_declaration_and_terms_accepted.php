<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add terms_accepted to the old warranty_records table (used by public form)
        if (!Schema::hasColumn('warranty_records', 'terms_accepted')) {
            Schema::table('warranty_records', function (Blueprint $table) {
                $table->tinyInteger('terms_accepted')->default(0)->after('warranty_status');
            });
        }

        // Add warranty_declaration_text to general_settings
        if (!Schema::hasColumn('general_settings', 'warranty_declaration_text')) {
            Schema::table('general_settings', function (Blueprint $table) {
                $table->longText('warranty_declaration_text')->nullable()->after('shipping_returns_content');
            });
        }

        // Seed default declaration text
        $defaultText = 'I hereby confirm, declare, acknowledge, understand, and voluntarily agree that I have fully read, carefully understood, and completely accepted all KINGSTER warranty terms, conditions, policies, limitations, exclusions, disclaimers, replacement rules, service policies, and customer responsibilities mentioned by the company through invoice, website, packaging, warranty card, seller communication, product listing, or service process. I understand and agree that KINGSTER warranty is strictly limited only to manufacturing defects and internal hardware malfunction occurring under normal and proper usage conditions during the applicable warranty period from original invoice date.

I clearly understand, acknowledge, and agree that physical damage, accidental damage, customer-caused damage, misuse, mishandling, negligence, liquid/water damage, moisture damage, humidity damage, rust/corrosion, overheating, voltage fluctuation damage, power surge damage, burnt components, short circuit damage, broken body, broken PCB, broken connectors, broken USB ports, scratches, dents, cosmetic damage, improper installation, improper usage, unauthorized software/firmware modification, unsupported usage, virus infection, malware infection, software corruption, operating system issues, compatibility issues, third-party application issues, courier/transit damage, improper packing, opened products, repaired products, modified products, tampered serial numbers, removed warranty stickers, duplicate/fake products, unauthorized repairs, or any external/customer-related damages are strictly excluded and not covered under KINGSTER warranty policy under any circumstances.

I further fully understand, acknowledge, accept, and agree that all data, files, folders, documents, business records, software, applications, operating systems, photos, videos, confidential information, private information, passwords, licenses, databases, and any other digital content stored inside the device/product are completely and solely my personal responsibility only. I confirm and declare that before submitting product for any warranty claim, inspection, diagnosis, service, repair, replacement, or technical verification, I have already taken full and proper backup of all important data from the device. I understand and voluntarily agree that during testing, inspection, diagnosis, quality checking, technical verification, repair process, firmware update, software process, refurbishing process, replacement process, formatting process, resetting process, or internal checking process, the product/device may be opened, reset, repaired, reformatted, tested, refurbished, dismantled, or replaced completely and all stored data inside the device may be permanently deleted, erased, formatted, corrupted, damaged, become inaccessible, or become permanently unrecoverable without any prior notice.

I clearly understand and agree that KINGSTER does not provide, guarantee, promise, or include any type of data protection service, backup service, recovery service, restoration service, recovery expense reimbursement, software restoration support, privacy protection assurance, or data security guarantee under warranty policy. I voluntarily accept and agree that KINGSTER, its owners, directors, employees, staff members, distributors, dealers, resellers, partners, logistics providers, technical teams, service centers, affiliates, contractors, or representatives shall not be held responsible, liable, accountable, or answerable in any manner whatsoever for any type of deleted data, corrupted files, inaccessible files, missing documents, software corruption, operating system failure, privacy loss, confidential information leakage, business interruption, financial loss, commercial loss, recovery expenses, mental stress, inconvenience, emotional loss, indirect damages, incidental damages, special damages, consequential damages, loss of profits, third-party liabilities, customer claims, or any direct or indirect damages arising due to product malfunction, storage failure, warranty process, testing process, replacement process, repair process, technical inspection, formatting process, shipment delay, service delay, or device failure under any condition whatsoever.

I also clearly understand, acknowledge, and voluntarily agree that warranty replacement, repair, inspection, diagnosis, verification, or claim approval process shall begin only after the defective/damaged product is physically received at authorized KINGSTER service location, warehouse, office, or technical department. I understand and agree that no advance replacement, immediate replacement, temporary replacement, compensation, refund, exchange, upgrade, reimbursement, or warranty approval shall be provided before physical receipt, verification, inspection, and approval of the product by KINGSTER technical team. I further understand that product shipping time, pickup time, courier delay, transit delay, verification time, inspection time, testing time, and approval process time are separate from actual warranty processing time and KINGSTER shall not be responsible for delays caused due to courier companies, logistics providers, natural events, workload, technical process, verification requirements, stock availability, or operational delays.

I further understand and voluntarily agree that KINGSTER reserves complete and absolute rights to inspect, test, diagnose, repair, reject, approve, replace, refurbish, retain, or return the product according to company policy and technical inspection findings. I understand and accept that replacement product may be repaired, refurbished, equivalent, alternative, or similar specification model and exact same model, design, color, packaging, manufacturing batch, or appearance may not always be available during replacement process. I also understand that replacement product warranty shall continue only from original purchase invoice date and no fresh/new warranty period shall start after replacement or repair.

I fully understand and voluntarily agree that KINGSTER warranty support is strictly limited only to repair or replacement of eligible product as decided solely by KINGSTER technical inspection team. Refund, return, cash compensation, upgrade demand, exchange demand, loss reimbursement, goodwill compensation, courier reimbursement, recovery reimbursement, mental harassment claim, business loss claim, or any additional commercial/legal claim shall not be applicable or entertained under warranty policy under any condition whatsoever.

I further confirm and declare that original invoice/bill is mandatory and compulsory for all warranty claims and KINGSTER reserves complete rights to reject warranty claim if invoice is missing, unclear, altered, fake, incomplete, mismatched, or suspicious. I understand and agree that final technical inspection report, testing result, claim approval decision, replacement decision, repair decision, rejection decision, and policy interpretation made by KINGSTER technical department shall be final, binding, conclusive, and fully acceptable to me without objection or dispute.

By ticking/checking/accepting this declaration digitally or physically, I voluntarily, knowingly, and unconditionally agree to all KINGSTER warranty terms, conditions, exclusions, limitations, responsibilities, liabilities, disclaimers, service policies, and legal conditions mentioned above without any objection, dispute, misunderstanding, or future claim.';

        DB::table('general_settings')->whereNotNull('id')->update([
            'warranty_declaration_text' => $defaultText,
        ]);
    }

    public function down(): void
    {
        Schema::table('warranty_records', function (Blueprint $table) {
            $table->dropColumn('terms_accepted');
        });
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn('warranty_declaration_text');
        });
    }
};
