<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\ApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PayoutOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public Order $order
    ) {}

    /**
     * Use the API service to send a payout of the correct amount.
     * Note: The order status must be paid if the payout is successful, or remain unpaid in the event of an exception.
     *
     * @return void
     */
    public function handle(ApiService $apiService)
    {
        try {
            // Use the ApiService to send a payout for the order
            $payoutAmount = $this->calculatePayoutAmount($this->order); // Replace with your payout calculation logic
            $apiService->sendPayout($this->order->affiliate->api_key, $payoutAmount);

            // Update the order status to paid
            $this->order->update(['paid' => true]);
        } catch (\Exception $e) {
            // Log the exception or handle it appropriately
            // For example, you might log the error and keep the order status as unpaid
            Log::error('Payout failed for order ' . $this->order->id . ': ' . $e->getMessage());

            // You might want to re-throw the exception if you want it to be reported in Laravel's job failure handling
            // throw $e;
        }
    }

    private function calculatePayoutAmount(Order $order): float
    {
        // TODO: Implement logic to calculate the payout amount based on the order details
        // You may need to consider order total, commission rates, etc.

        return 0.0; // Placeholder, replace with actual calculation
    }
}
