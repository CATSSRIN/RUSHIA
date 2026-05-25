<?php

namespace Tests\Feature\Admin;

use App\Models\RansumUpload;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RansumTotalPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_download_total_pdf_successfully(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $upload = RansumUpload::create([
            'original_filename' => 'test_ransum.xlsx',
            'stored_filename' => 'test_ransum.xlsx',
            'file_hash' => 'xyz123',
            'vessel_name' => 'KM TEST VESSEL',
            'status' => 'imported',
            'uploaded_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.ransum.total.download', $upload->id), [
            'html_content' => '<h1>Total Ransum Test Table</h1><table><tr><td>Item A</td></tr></table>',
        ]);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertHeader('Content-Disposition', 'attachment; filename=Total-Ransum-KM_TEST_VESSEL.pdf');
    }

    public function test_non_admin_cannot_download_total_pdf(): void
    {
        $nonAdmin = User::factory()->create(['is_admin' => false]);
        $admin = User::factory()->create(['is_admin' => true]);

        $upload = RansumUpload::create([
            'original_filename' => 'test_ransum.xlsx',
            'stored_filename' => 'test_ransum.xlsx',
            'file_hash' => 'xyz123',
            'vessel_name' => 'KM TEST VESSEL',
            'status' => 'imported',
            'uploaded_by' => $admin->id,
        ]);

        $response = $this->actingAs($nonAdmin)->post(route('admin.ransum.total.download', $upload->id), [
            'html_content' => '<h1>Test</h1>',
        ]);

        $response->assertStatus(403);
    }
}
