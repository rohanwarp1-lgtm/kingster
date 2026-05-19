<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\FbaAuto\Models\FbaAuto;
use App\Modules\Warranty\Models\WarrantyRegistration;
use App\Modules\Rma\Models\RmaTicket;
use App\Modules\ReturnReport\Models\ReturnReport;

class ModuleDataSeeder extends Seeder
{
    public function run(): void
    {
        FbaAuto::updateOrCreate(
            ['shipment_id' => 'SHIP-0001'],
            [
                'shipment_date' => now()->toDateString(),
                'shipment_time' => now()->format('H:i'),
                'product_name' => 'Demo Product',
                'qty' => 10,
                'state' => 'Gujarat',
                'warehouse_name' => 'Main Warehouse',
                'qty_price' => 1999.00,
                'generated_by' => 1,
                'updated_by' => 1,
                'status' => 'pending',
            ]
        );

        WarrantyRegistration::updateOrCreate(
            ['serial_number' => 'SN-W-001'],
            [
                'customer_name' => 'Demo Customer',
                'mobile' => '9999999999',
                'email' => 'customer@example.com',
                'product_name' => 'Demo Product',
                'model' => 'Model X',
                'price' => 2999.00,
                'purchase_date' => now()->subDays(7)->toDateString(),
                'purchase_platform' => 'Amazon',
                'order_id' => 'ORD-1001',
                'warranty_type' => 'standard',
                'status' => 'pending',
                'approval_notes' => null,
                'approved_by' => 1,
            ]
        );

        RmaTicket::updateOrCreate(
            ['order_id' => 'ORD-2001'],
            [
                'customer_name' => 'Demo RMA Customer',
                'mobile' => '8888888888',
                'email' => 'rma@example.com',
                'order_date' => now()->subDays(5)->toDateString(),
                'product_name' => 'Demo Product',
                'model' => 'Model X',
                'platform' => 'amazon',
                'issue_type' => 'hardware_defect',
                'issue_description' => 'Device not powering on.',
                'address' => 'Demo Address, India',
                'replacement_type' => 'full',
                'assigned_to' => 1,
                'status' => 'open',
            ]
        );

        ReturnReport::updateOrCreate(
            ['order_id' => 'AMZ-3001'],
            [
                'product_name' => 'Demo Product',
                'model_name' => 'Model X',
                'marketplace' => 'amazon',
                'return_reason' => 'damaged',
                'refund_status' => 'pending',
                'return_cost' => 150.00,
                'loss_amount' => 500.00,
                'warehouse' => 'Main Warehouse',
            ]
        );
    }
}
