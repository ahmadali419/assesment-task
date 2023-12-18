<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\Merchant;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class ApiService
{
    /**
     * Create a new discount code for an affiliate
     *
     * @param Merchant $merchant
     *
     * @return array{id: int, code: string}
     */
    public function createDiscountCode(Merchant $merchant): array
    {
        return [
            'id' => rand(0, 100000),
            'code' => Str::uuid()
        ];
    }

    /**
     * Send a payout to an email
     *
     * @param  string $email
     * @param  float $amount
     * @return void
     * @throws RuntimeException
     */
    public function sendPayout(string $email, float $amount)
    {
        // Example: Send an email as a payout
        try {
            Mail::to($email)->send(new PayoutEmail($amount));
        } catch (\Exception $e) {
            // Log the exception or handle it appropriately
            throw new RuntimeException('Failed to send payout: ' . $e->getMessage());
        }
    }
}
