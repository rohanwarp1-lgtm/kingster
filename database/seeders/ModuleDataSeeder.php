<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\FbaAuto\Models\FbaAuto;
use App\Modules\FbaAuto\Models\FbaWarehouse;
use App\Modules\FbaAuto\Models\FbaState;
use App\Modules\Warranty\Models\WarrantyRegistration;
use App\Modules\Rma\Models\RmaTicket;
use App\Modules\ReturnReport\Models\ReturnReport;
use App\Models\ProductName;

class ModuleDataSeeder extends Seeder
{
    public function run(): void
    {
        $userId = \App\Models\User::first()?->id ?? 1;

        // Seed product names
        $productNames = ['Samsung 870 EVO SSD 500GB', 'WD Blue HDD 1TB', 'Kingston RAM 8GB DDR4', 'Logitech MX Keys', 'Anker USB Hub'];
        foreach ($productNames as $name) {
            ProductName::firstOrCreate(['name' => $name], ['is_deleted' => 0, 'created_by' => $userId, 'modified_by' => $userId]);
        }

        // Seed warehouses
        $warehouses = [
            ['name' => 'Mumbai Warehouse', 'sort_order' => 1],
            ['name' => 'Delhi Warehouse',  'sort_order' => 2],
            ['name' => 'Chennai Hub',      'sort_order' => 3],
        ];
        foreach ($warehouses as $wh) {
            FbaWarehouse::firstOrCreate(['name' => $wh['name']], ['sort_order' => $wh['sort_order'], 'is_active' => true]);
        }

        // Seed states
        $states = [
            ['name' => 'Maharashtra', 'code' => 'MH'],
            ['name' => 'Delhi',       'code' => 'DL'],
            ['name' => 'Tamil Nadu',  'code' => 'TN'],
            ['name' => 'Gujarat',     'code' => 'GJ'],
            ['name' => 'Karnataka',   'code' => 'KA'],
        ];
        foreach ($states as $st) {
            FbaState::firstOrCreate(['name' => $st['name']], ['code' => $st['code'], 'is_active' => true, 'sort_order' => 0]);
        }

        // FBA Shipments – one shipment_id per group, multiple items each, all statuses covered
        $shipmentGroups = [
            [
                'shipment_id'    => 'FBA-2026-001',
                'shipment_date'  => now()->subDays(10)->toDateString(),
                'state'          => 'Maharashtra',
                'warehouse_name' => 'Mumbai Warehouse',
                'status'         => 'pending',
                'items'          => [
                    ['product_name' => 'Samsung 870 EVO SSD 500GB', 'qty' => 50,  'qty_price' => 74950.00],
                    ['product_name' => 'WD Blue HDD 1TB',            'qty' => 30,  'qty_price' => 38850.00],
                ],
            ],
            [
                'shipment_id'    => 'FBA-2026-002',
                'shipment_date'  => now()->subDays(7)->toDateString(),
                'state'          => 'Delhi',
                'warehouse_name' => 'Delhi Warehouse',
                'status'         => 'processing',
                'items'          => [
                    ['product_name' => 'Kingston RAM 8GB DDR4', 'qty' => 100, 'qty_price' => 179900.00],
                ],
            ],
            [
                'shipment_id'    => 'FBA-2026-003',
                'shipment_date'  => now()->subDays(5)->toDateString(),
                'state'          => 'Tamil Nadu',
                'warehouse_name' => 'Chennai Hub',
                'status'         => 'shipped',
                'items'          => [
                    ['product_name' => 'Logitech MX Keys', 'qty' => 20, 'qty_price' => 159800.00],
                    ['product_name' => 'Anker USB Hub',    'qty' => 40, 'qty_price' => 39960.00],
                ],
            ],
            [
                'shipment_id'    => 'FBA-2026-004',
                'shipment_date'  => now()->subDays(15)->toDateString(),
                'state'          => 'Gujarat',
                'warehouse_name' => 'Mumbai Warehouse',
                'status'         => 'delivered',
                'items'          => [
                    ['product_name' => 'Samsung 870 EVO SSD 500GB', 'qty' => 80, 'qty_price' => 119920.00],
                ],
            ],
            [
                'shipment_id'    => 'FBA-2026-005',
                'shipment_date'  => now()->subDays(20)->toDateString(),
                'state'          => 'Karnataka',
                'warehouse_name' => 'Delhi Warehouse',
                'status'         => 'cancelled',
                'items'          => [
                    ['product_name' => 'WD Blue HDD 1TB', 'qty' => 10, 'qty_price' => 12950.00],
                ],
            ],
            [
                'shipment_id'    => 'FBA-2026-006',
                'shipment_date'  => now()->subDays(25)->toDateString(),
                'state'          => 'Maharashtra',
                'warehouse_name' => 'Chennai Hub',
                'status'         => 'closed',
                'items'          => [
                    ['product_name' => 'Kingston RAM 8GB DDR4', 'qty' => 60, 'qty_price' => 107940.00],
                    ['product_name' => 'Anker USB Hub',         'qty' => 25, 'qty_price' => 24975.00],
                ],
            ],
            [
                'shipment_id'    => 'FBA-2026-007',
                'shipment_date'  => now()->subDays(3)->toDateString(),
                'state'          => 'Delhi',
                'warehouse_name' => 'Mumbai Warehouse',
                'status'         => 'returned',
                'items'          => [
                    ['product_name' => 'Logitech MX Keys', 'qty' => 5, 'qty_price' => 39950.00],
                ],
            ],
        ];

        foreach ($shipmentGroups as $group) {
            $header = [
                'shipment_date'  => $group['shipment_date'],
                'state'          => $group['state'],
                'warehouse_name' => $group['warehouse_name'],
                'generated_by'   => $userId,
                'updated_by'     => $userId,
                'status'         => $group['status'],
            ];
            foreach ($group['items'] as $item) {
                FbaAuto::firstOrCreate(
                    ['shipment_id' => $group['shipment_id'], 'product_name' => $item['product_name']],
                    array_merge($header, ['qty' => $item['qty'], 'qty_price' => $item['qty_price']])
                );
            }
        }

        // Warranty Registrations – all statuses
        $warranties = [
            ['serial' => 'SN-W-001', 'status' => 'pending',      'customer' => 'Rahul Sharma',  'product' => 'Samsung 870 EVO SSD 500GB', 'platform' => 'amazon',    'days' => 10],
            ['serial' => 'SN-W-002', 'status' => 'under_review', 'customer' => 'Priya Patel',   'product' => 'WD Blue HDD 1TB',           'platform' => 'flipkart',  'days' => 20],
            ['serial' => 'SN-W-003', 'status' => 'approved',     'customer' => 'Amit Verma',    'product' => 'Kingston RAM 8GB DDR4',     'platform' => 'direct',    'days' => 45],
            ['serial' => 'SN-W-004', 'status' => 'rejected',     'customer' => 'Sunita Gupta',  'product' => 'Logitech MX Keys',          'platform' => 'amazon',    'days' => 60],
            ['serial' => 'SN-W-005', 'status' => 'expired',      'customer' => 'Vikram Singh',  'product' => 'Anker USB Hub',             'platform' => 'meesho',    'days' => 400],
            ['serial' => 'SN-W-006', 'status' => 'cancelled',    'customer' => 'Neha Joshi',    'product' => 'Samsung 870 EVO SSD 500GB', 'platform' => 'snapdeal',  'days' => 5],
        ];

        foreach ($warranties as $w) {
            if (!WarrantyRegistration::where('serial_number', $w['serial'])->exists()) {
                $purchaseDate = now()->subDays($w['days'])->toDateString();
                WarrantyRegistration::create([
                    'customer_name'     => $w['customer'],
                    'mobile'            => '98765' . rand(10000, 99999),
                    'email'             => strtolower(str_replace(' ', '.', $w['customer'])) . '@example.com',
                    'product_name'      => $w['product'],
                    'model'             => 'Standard Model',
                    'serial_number'     => $w['serial'],
                    'price'             => rand(1000, 9999) . '.00',
                    'purchase_date'     => $purchaseDate,
                    'purchase_platform' => $w['platform'],
                    'order_id'          => 'ORD-' . strtoupper(substr(md5($w['serial']), 0, 8)),
                    'warranty_type'     => 'standard',
                    'status'            => $w['status'],
                    'approval_notes'    => $w['status'] === 'rejected' ? 'Serial number not found in system' : null,
                    'approved_by'       => $userId,
                ]);
            }
        }

        // RMA Tickets – all statuses
        $rmaTickets = [
            ['order' => 'ORD-RMA-001', 'status' => 'open',                 'customer' => 'Rohan Mehta',   'product' => 'Samsung 870 EVO SSD 500GB', 'issue' => 'hardware_defect', 'platform' => 'amazon'],
            ['order' => 'ORD-RMA-002', 'status' => 'under_review',         'customer' => 'Anjali Kumar',  'product' => 'WD Blue HDD 1TB',           'issue' => 'software_issue',  'platform' => 'flipkart'],
            ['order' => 'ORD-RMA-003', 'status' => 'approved',             'customer' => 'Deepak Nair',   'product' => 'Kingston RAM 8GB DDR4',     'issue' => 'missing_parts',   'platform' => 'other'],
            ['order' => 'ORD-RMA-004', 'status' => 'pickup_pending',       'customer' => 'Meera Shah',    'product' => 'Logitech MX Keys',          'issue' => 'wrong_item',      'platform' => 'amazon'],
            ['order' => 'ORD-RMA-005', 'status' => 'pickup_completed',     'customer' => 'Arjun Reddy',   'product' => 'Anker USB Hub',             'issue' => 'damaged',         'platform' => 'other'],
            ['order' => 'ORD-RMA-006', 'status' => 'replacement_shipped',  'customer' => 'Kavya Iyer',    'product' => 'Samsung 870 EVO SSD 500GB', 'issue' => 'hardware_defect', 'platform' => 'other'],
            ['order' => 'ORD-RMA-007', 'status' => 'rejected',             'customer' => 'Sanjay Tiwari', 'product' => 'WD Blue HDD 1TB',           'issue' => 'hardware_defect', 'platform' => 'flipkart'],
            ['order' => 'ORD-RMA-008', 'status' => 'closed',               'customer' => 'Divya Rao',     'product' => 'Kingston RAM 8GB DDR4',     'issue' => 'software_issue',  'platform' => 'other'],
        ];

        foreach ($rmaTickets as $rma) {
            if (!RmaTicket::where('order_id', $rma['order'])->exists()) {
                RmaTicket::create([
                    'customer_name'     => $rma['customer'],
                    'mobile'            => '87654' . rand(10000, 99999),
                    'email'             => strtolower(str_replace(' ', '.', $rma['customer'])) . '@example.com',
                    'order_date'        => now()->subDays(rand(1, 30))->toDateString(),
                    'order_id'          => $rma['order'],
                    'product_name'      => $rma['product'],
                    'model'             => 'Standard Model',
                    'platform'          => $rma['platform'],
                    'issue_type'        => $rma['issue'],
                    'issue_description' => 'Customer reported: ' . str_replace('_', ' ', $rma['issue']) . '. Needs inspection.',
                    'address'           => '123, Test Street, Mumbai, MH 400001',
                    'replacement_type'  => 'full',
                    'assigned_to'       => $userId,
                    'status'            => $rma['status'],
                ]);
            }
        }

        // Return Reports – all marketplaces, statuses, reasons
        $returnReports = [
            ['order' => 'AMZ-001', 'marketplace' => 'amazon',   'reason' => 'damaged',         'refund' => 'pending',   'product' => 'Samsung 870 EVO SSD 500GB', 'warehouse' => 'Mumbai Warehouse', 'cost' => 250.00, 'loss' => 1499.00],
            ['order' => 'FLP-001', 'marketplace' => 'flipkart', 'reason' => 'wrong_item',     'refund' => 'processed', 'product' => 'WD Blue HDD 1TB',           'warehouse' => 'Delhi Warehouse',  'cost' => 100.00, 'loss' => 1295.00],
            ['order' => 'AMZ-002', 'marketplace' => 'amazon',   'reason' => 'not_as_described','refund' => 'rejected',  'product' => 'Kingston RAM 8GB DDR4',     'warehouse' => 'Chennai Hub',      'cost' => 150.00, 'loss' => 1799.00],
            ['order' => 'DIR-001', 'marketplace' => 'other',    'reason' => 'defective',      'refund' => 'pending',   'product' => 'Logitech MX Keys',          'warehouse' => 'Mumbai Warehouse', 'cost' => 200.00, 'loss' => 7990.00],
            ['order' => 'MEE-001', 'marketplace' => 'other',    'reason' => 'buyer_remorse',  'refund' => 'processed', 'product' => 'Anker USB Hub',             'warehouse' => 'Delhi Warehouse',  'cost' => 80.00,  'loss' => 999.00],
            ['order' => 'AMZ-003', 'marketplace' => 'amazon',   'reason' => 'damaged',        'refund' => 'processed', 'product' => 'Samsung 870 EVO SSD 500GB', 'warehouse' => 'Chennai Hub',      'cost' => 300.00, 'loss' => 1499.00],
        ];

        foreach ($returnReports as $rr) {
            ReturnReport::firstOrCreate(
                ['order_id' => $rr['order']],
                [
                    'product_name'  => $rr['product'],
                    'model_name'    => 'Standard Model',
                    'marketplace'   => $rr['marketplace'],
                    'return_reason' => $rr['reason'],
                    'refund_status' => $rr['refund'],
                    'return_cost'   => $rr['cost'],
                    'loss_amount'   => $rr['loss'],
                    'warehouse'     => $rr['warehouse'],
                ]
            );
        }

        $this->command->info('Module dummy data seeded successfully.');
    }
}
