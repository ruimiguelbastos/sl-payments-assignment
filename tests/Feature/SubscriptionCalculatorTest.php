<?php

namespace Tests\Feature;

use App\Calculators\SubscriptionCalculator;
use App\Subscription;
use Carbon\Carbon;
use Tests\TestCase;

class SubscriptionCalculatorTest extends TestCase
{    
    public function testData(): void
    {
        $now     = Carbon::createFromFormat('Y-m-d H:i:s', '2024-08-14 17:31:54');
        $subject = new SubscriptionCalculator();
        
        $result = $subject->calculateSubscriptionPaymentsForTheNextYear(
                Subscription::all(),
                $now,
            );
        
       self::assertEquals(
           [
               'data' => [
                   'sub_1' => [
                        'prices' => [55, 0, 0, 55, 0, 0, 55, 0, 0, 55, 0, 0],
                        'totalPrice' => 220,
                        'customerEmail' => 'koepp.maye@example.org',
                    ],
                    'sub_2' => [
                        'prices' => [90, 0, 0, 0, 0, 0, 90, 0, 0, 0, 0, 0],
                        'totalPrice' => 180,
                        'customerEmail' => 'koepp.maye@example.org',
                    ],
                    'sub_3' => [
                        'prices' => [20, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                        'totalPrice' => 20,
                        'customerEmail' => 'koepp.maye@example.org',
                    ],
                    'sub_4' => [
                        'prices' => [55, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                        'totalPrice' => 55,
                        'customerEmail' => 'jbeatty@example.org',
                    ],
                    'sub_5' => [
                        'prices' => [0, 0, 0, 90, 0, 0, 0, 0, 0, 90, 0, 0],
                        'totalPrice' => 180,
                        'customerEmail' => 'jbeatty@example.org',
                    ],
                    'sub_6' => [
                        'prices' => [15, 15, 15, 20, 20, 20, 20, 20, 20, 20, 20, 20],
                        'totalPrice' => 225,
                        'customerEmail' => 'jbeatty@example.org',
                    ],
                ],
                'monthsTotals' => [235, 15, 15, 165, 20, 20, 165, 20, 20, 165, 20, 20],
                'yearTotals' => 880,
            ],
            json_decode(json_encode($result->transform()), true),
       );
    }
}
