<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'customer_name')) {
                $table->string('customer_name', 120)->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('orders', 'customer_email')) {
                $table->string('customer_email', 190)->nullable()->after('customer_name');
            }
            if (!Schema::hasColumn('orders', 'customer_phone')) {
                $table->string('customer_phone', 40)->nullable()->after('customer_email');
            }
            if (!Schema::hasColumn('orders', 'customer_address')) {
                $table->string('customer_address', 255)->nullable()->after('customer_phone');
            }
            if (!Schema::hasColumn('orders', 'notes')) {
                $table->string('notes', 500)->nullable()->after('customer_address');
            }

            if (!Schema::hasColumn('orders', 'fulfillment_method')) {
                $table->string('fulfillment_method', 20)->default('delivery')->after('notes');
            }

            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method', 30)->default('cash')->after('fulfillment_method');
            }
            if (!Schema::hasColumn('orders', 'payment_reference')) {
                $table->string('payment_reference', 80)->nullable()->after('payment_method');
            }

            if (!Schema::hasColumn('orders', 'subtotal_amount')) {
                $table->decimal('subtotal_amount', 10, 2)->default(0)->after('payment_reference');
            }
            if (!Schema::hasColumn('orders', 'shipping_fee')) {
                $table->decimal('shipping_fee', 10, 2)->default(0)->after('subtotal_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $drop = [];
            foreach ([
                'customer_name',
                'customer_email',
                'customer_phone',
                'customer_address',
                'notes',
                'fulfillment_method',
                'payment_method',
                'payment_reference',
                'subtotal_amount',
                'shipping_fee',
            ] as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $drop[] = $col;
                }
            }

            if ($drop !== []) {
                $table->dropColumn($drop);
            }
        });
    }
};

