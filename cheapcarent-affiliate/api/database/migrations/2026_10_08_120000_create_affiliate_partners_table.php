<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('affiliate_partners', function (Blueprint $table): void {
            $table->id();
            $table->foreignUuid('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('display_name', 160);
            $table->string('website_url', 500);
            $table->string('promotion_description', 1000);
            $table->string('status', 24)->default('pending')->index();
            $table->string('public_code', 32)->unique();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_partners');
    }
};
