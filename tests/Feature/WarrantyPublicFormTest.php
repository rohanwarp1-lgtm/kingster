<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ProductName;

class WarrantyPublicFormTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(): array
    {
        return [
            'buyer_name'      => 'Test Buyer',
            'mobile'          => '9876543210',
            'email'           => 'test@example.com',
            'purchase_source' => 'Online',
            'address'         => 'Amazon',
            'product_name'    => 'Test Product',
            'serial_number'   => 'SN-TEST-001',
            'purchase_date'   => '2024-01-15',
        ];
    }

    public function test_get_request_is_rejected(): void
    {
        $this->get('/store-warranty')->assertStatus(405);
    }

    public function test_valid_submission_creates_warranty(): void
    {
        $response = $this->postJson('/store-warranty', $this->validPayload());
        $response->assertStatus(200)->assertJson(['status' => 200]);
        $this->assertDatabaseHas('warranty_records', ['serial_number' => 'SN-TEST-001']);
    }

    public function test_duplicate_serial_is_rejected(): void
    {
        $this->postJson('/store-warranty', $this->validPayload());
        $response = $this->postJson('/store-warranty', $this->validPayload());
        $response->assertStatus(200)->assertJson(['status' => 400]);
        $this->assertSame(1, \App\Models\Warranty::where('serial_number', 'SN-TEST-001')->count());
    }

    public function test_missing_required_fields_returns_422(): void
    {
        $this->postJson('/store-warranty', [])->assertStatus(422);
    }

    public function test_created_by_is_not_hardcoded_to_1(): void
    {
        $this->postJson('/store-warranty', $this->validPayload());
        $record = \App\Models\Warranty::where('serial_number', 'SN-TEST-001')->first();
        $this->assertNull($record->created_by);
    }
}
