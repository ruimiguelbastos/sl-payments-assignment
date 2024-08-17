<?php

namespace App\Clients;

use Stripe\Collection as StripeCollection;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Stripe\TestHelpers\TestClock;

final class StripeBaseClient
{
    public function __construct(
        private StripeClient $client,
    ) {
    }

    /**
     * @throws ApiErrorException
     */
    public function getProducts(): StripeCollection
    {
        return $this->client->products->all();
    }

    /**
     * @throws ApiErrorException
     */
    public function getSubscriptions(): StripeCollection
    {
        return $this->client->subscriptions->all(['test_clock' => env('STRIPE_TEST_CLOCK')]);
    }

    /**
     * @throws ApiErrorException
     */
    public function getCustomers(): StripeCollection
    {
        return $this->client->customers->all(['test_clock' => env('STRIPE_TEST_CLOCK')]);
    }

    /**
     * @throws ApiErrorException
     */
    public function getTestClock(): TestClock
    {
        return $this->client->testHelpers->testClocks->retrieve(env('STRIPE_TEST_CLOCK'));
    }

}
