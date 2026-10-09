<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('affiliate_clicks', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignId('affiliate_partner_id')->constrained()->cascadeOnDelete();
            $table->string('destination', 500);
            $table->string('sub_id', 80)->nullable();
            $table->char('dedupe_key', 64)->unique();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['affiliate_partner_id', 'created_at']);
        });

        Schema::create('affiliate_order_attributions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('affiliate_partner_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('affiliate_click_id')->constrained('affiliate_clicks')->cascadeOnDelete();
            $table->foreignUuid('order_id')->unique()->constrained('orders')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['affiliate_partner_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_order_attributions');
        Schema::dropIfExists('affiliate_clicks');
    }
};
