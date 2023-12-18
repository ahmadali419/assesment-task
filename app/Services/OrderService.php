<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;

class OrderService
{
    public function __construct(
        protected AffiliateService $affiliateService
    ) {
    }

    /**
     * Process an order and log any commissions.
     * This should create a new affiliate if the customer_email is not already associated with one.
     * This method should also ignore duplicates based on order_id.
     *
     * @param  array{order_id: string, subtotal_price: float, merchant_domain: string, discount_code: string, customer_email: string, customer_name: string} $data
     * @return void
     */
    public function processOrder(array $data)
    {
        // Check if order with the same order_id already exists
        $existingOrder = Order::where('order_id', $data['order_id'])->first();

        // If the order already exists, ignore processing
        if ($existingOrder) {
            return;
        }

        // Check if an affiliate with the given user_id exists
        $affiliate = Affiliate::where('user_id', $data['user_id'])->first();

        // If no affiliate exists, create a new affiliate
        if (!$affiliate) {
            $merchant = Merchant::where('domain', $data['merchant_domain'])->first();
            $discountCode = $data['discount_code'];

            $affiliate = $this->affiliateService->register($merchant, $data['customer_email'], $data['customer_name'], $merchant->default_commission_rate, $discountCode);
        }

        // Log the order details and associate it with the affiliate
        $order = new Order([
            'order_id' => $data['order_id'],
            'subtotal' => $data['subtotal_price'],
            'merchant_id' => $merchant->id, // Assuming you have a $merchant variable
            'affiliate_id' => $affiliate->id, // Assuming you have a $affiliate variable
            'merchant_domain' => $data['merchant_domain'],
            'discount_code' => $data['discount_code'],
        ]);

        // Save the order
        $order->save();
    }
}
