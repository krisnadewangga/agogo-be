<?php

namespace Tests\Feature;

use App\Item;
use App\TargetProduksi;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SheetWebhookControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('target_produksi');
        Schema::dropIfExists('item');

        Schema::create('item', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->nullable();
            $table->string('nama_item')->nullable();
            $table->timestamps();
        });

        Schema::create('target_produksi', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('item_id');
            $table->decimal('target_produksi', 12, 2)->default(0);
            $table->date('target_date')->nullable();
            $table->timestamps();
        });
    }

    public function test_it_creates_target_produksi_from_sheet_payload()
    {
        Item::create([
            'code' => 'ABC-001',
            'nama_item' => 'Test Item',
        ]);

        $response = $this->postJson('/api/google-sheets', [
            'data' => [[
                'kode_item' => 'ABC-001',
                'tanggal' => '2026-08-04',
                'target_produksi' => 125,
            ]],
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('processed', 1);

        $this->assertDatabaseHas('target_produksi', [
            'item_id' => 1,
            'target_produksi' => '125.00',
            'target_date' => '2026-08-04',
        ]);
    }
}
