<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->default('General');
            $table->string('sku')->unique();
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->integer('stock')->default(10);
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('voice_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->string('phone_number')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('channel')->default('simulator'); // 'whatsapp', 'simulator'
            $table->string('status')->default('active'); // 'active', 'closed', 'handed_off'
            $table->json('context')->nullable(); // holds ongoing conversation state / cart / slot filling
            $table->timestamp('last_activity')->nullable();
            $table->timestamps();
        });

        Schema::create('voice_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voice_conversation_id')->constrained()->cascadeOnDelete();
            $table->enum('sender', ['user', 'bot']);
            $table->enum('type', ['voice', 'text'])->default('voice');
            $table->string('audio_path')->nullable();
            $table->text('transcript')->nullable();
            $table->string('intent')->nullable(); // 'product_inquiry', 'stock_check', 'book_order', 'order_status', 'faq', 'greeting'
            $table->json('entities')->nullable(); // extracted entities (products, quantity, address, phone)
            $table->text('response_text')->nullable();
            $table->string('response_audio_path')->nullable();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('voice_conversation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('delivery_address');
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_method')->default('Cash on Delivery (COD)');
            $table->enum('status', ['pending', 'confirmed', 'dispatched', 'delivered', 'cancelled'])->default('confirmed');
            $table->string('channel')->default('whatsapp_voice');
            $table->text('voice_transcript')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->decimal('price', 10, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });

        Schema::create('bot_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('voice_messages');
        Schema::dropIfExists('voice_conversations');
        Schema::dropIfExists('products');
        Schema::dropIfExists('bot_settings');
    }
};
