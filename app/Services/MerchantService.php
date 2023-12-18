<?php

namespace App\Services;

use App\Jobs\PayoutOrderJob;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;

class MerchantService
{
    /**
     * Register a new user and associated merchant.
     * Hint: Use the password field to store the API key.
     * Hint: Be sure to set the correct user type according to the constants in the User model.
     *
     * @param array{domain: string, name: string, email: string, api_key: string} $data
     * @return Merchant
     */
    public function register(array $data): Merchant
    {
        // Create a new user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['api_key']),
            'type' => User::TYPE_MERCHANT, // Assuming MERCHANT_TYPE is a constant in the User model
        ]);

        $merchant = Merchant::create([
            'user_id' => $user->id,
            'domain' => $data['domain'],
        ]);

        return $merchant;

    }

    /**
     * Update the user
     *
     * @param array{domain: string, name: string, email: string, api_key: string} $data
     * @return void
     */
    public function updateMerchant(User $user, array $data)
    {
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['api_key']),
        ]);

        // Update the associated merchant's domain
        $user->merchant->update(['domain' => $data['domain']]);
    }

    /**
     * Find a merchant by their email.
     * Hint: You'll need to look up the user first.
     *
     * @param string $email
     * @return Merchant|null
     */
    public function findMerchantByEmail(string $email): ?Merchant
    {
        $user = User::where('email', $email)->first();

        // Return the associated merchant or null if not found
        return $user ? $user->merchant : null;
    }

    /**
     * Pay out all of an affiliate's orders.
     * Hint: You'll need to dispatch the job for each unpaid order.
     *
     * @param Affiliate $affiliate
     * @return void
     */
    public function payout(Affiliate $affiliate)
    {
        $unpaidOrders = $affiliate->orders()->where('paid', false)->get();

        // Dispatch a PayoutOrderJob for each unpaid order
        foreach ($unpaidOrders as $order) {
            dispatch(new PayoutOrderJob($order));
        }
    }
}
