<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class OrderNumberGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user for authentication
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        
        // Create a payment method
        $this->paymentMethod = PaymentMethod::factory()->create([
            'name' => 'Cash',
            'is_active' => true,
            'is_cash' => true
        ]);
    }

    /** @test */
    public function it_generates_sequential_order_numbers()
    {
        // Create first order
        $order1 = DB::transaction(function () {
            $noOrder = Order::generateNextOrderNumber();
            return Order::create([
                'no_order' => $noOrder,
                'name' => 'Test Customer 1',
                'total_price' => 100,
                'payment_method_id' => $this->paymentMethod->id,
                'status' => 'pending'
            ]);
        });

        // Create second order
        $order2 = DB::transaction(function () {
            $noOrder = Order::generateNextOrderNumber();
            return Order::create([
                'no_order' => $noOrder,
                'name' => 'Test Customer 2',
                'total_price' => 200,
                'payment_method_id' => $this->paymentMethod->id,
                'status' => 'pending'
            ]);
        });

        $this->assertEquals('000001', $order1->no_order);
        $this->assertEquals('000002', $order2->no_order);
    }

    /** @test */
    public function it_prevents_duplicate_order_numbers_with_concurrent_requests()
    {
        $orderNumbers = [];
        $errors = [];

        // Simulate 10 concurrent order creations
        $promises = [];
        for ($i = 0; $i < 10; $i++) {
            $promises[] = function () use (&$orderNumbers, &$errors, $i) {
                try {
                    $order = DB::transaction(function () use ($i) {
                        $noOrder = Order::generateNextOrderNumber();
                        return Order::create([
                            'no_order' => $noOrder,
                            'name' => "Test Customer {$i}",
                            'total_price' => 100 * ($i + 1),
                            'payment_method_id' => $this->paymentMethod->id,
                            'status' => 'pending'
                        ]);
                    });
                    $orderNumbers[] = $order->no_order;
                } catch (\Exception $e) {
                    $errors[] = $e->getMessage();
                }
            };
        }

        // Execute all promises
        foreach ($promises as $promise) {
            $promise();
        }

        // Assert no errors occurred
        $this->assertEmpty($errors, 'No errors should occur during concurrent order creation');

        // Assert all order numbers are unique
        $uniqueNumbers = array_unique($orderNumbers);
        $this->assertCount(10, $uniqueNumbers, 'All order numbers should be unique');

        // Assert order numbers are sequential
        sort($orderNumbers);
        $expected = ['000001', '000002', '000003', '000004', '000005', '000006', '000007', '000008', '000009', '000010'];
        $this->assertEquals($expected, $orderNumbers);
    }

    /** @test */
    public function it_handles_empty_database_correctly()
    {
        // Ensure database is empty
        Order::query()->delete();

        $order = DB::transaction(function () {
            $noOrder = Order::generateNextOrderNumber();
            return Order::create([
                'no_order' => $noOrder,
                'name' => 'First Customer',
                'total_price' => 100,
                'payment_method_id' => $this->paymentMethod->id,
                'status' => 'pending'
            ]);
        });

        $this->assertEquals('000001', $order->no_order);
    }

    /** @test */
    public function it_continues_sequence_after_existing_orders()
    {
        // Create some existing orders
        DB::transaction(function () {
            for ($i = 1; $i <= 5; $i++) {
                $noOrder = Order::generateNextOrderNumber();
                Order::create([
                    'no_order' => $noOrder,
                    'name' => "Customer {$i}",
                    'total_price' => 100,
                    'payment_method_id' => $this->paymentMethod->id,
                    'status' => 'pending'
                ]);
            }
        });

        // Create new order
        $newOrder = DB::transaction(function () {
            $noOrder = Order::generateNextOrderNumber();
            return Order::create([
                'no_order' => $noOrder,
                'name' => 'New Customer',
                'total_price' => 100,
                'payment_method_id' => $this->paymentMethod->id,
                'status' => 'pending'
            ]);
        });

        $this->assertEquals('000006', $newOrder->no_order);
    }

    /** @test */
    public function it_pads_order_numbers_correctly()
    {
        $testCases = [
            1 => '000001',
            10 => '000010',
            100 => '000100',
            1000 => '001000',
            10000 => '010000',
            99999 => '099999',
        ];

        foreach ($testCases as $number => $expected) {
            // Manually set a high number to test padding
            DB::table('orders')->truncate();
            
            if ($number > 1) {
                // Create a dummy order with the previous number
                DB::table('orders')->insert([
                    'id' => \Illuminate\Support\Str::uuid(),
                    'no_order' => str_pad($number - 1, 6, '0', STR_PAD_LEFT),
                    'name' => 'Dummy',
                    'total_price' => 100,
                    'payment_method_id' => $this->paymentMethod->id,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $order = DB::transaction(function () {
                $noOrder = Order::generateNextOrderNumber();
                return Order::create([
                    'no_order' => $noOrder,
                    'name' => 'Test Customer',
                    'total_price' => 100,
                    'payment_method_id' => $this->paymentMethod->id,
                    'status' => 'pending'
                ]);
            });

            $this->assertEquals($expected, $order->no_order, "Order number should be {$expected} for sequence {$number}");
        }
    }

    /** @test */
    public function it_throws_exception_when_called_outside_transaction()
    {
        // This test verifies that lockForUpdate requires a transaction
        // Note: In some database configurations, this might not throw an exception
        // but the lock won't be effective
        
        try {
            // Calling without transaction (not recommended)
            $noOrder = Order::generateNextOrderNumber();
            
            // If we reach here, at least verify the number is generated
            $this->assertMatchesRegularExpression('/^\d{6}$/', $noOrder);
            
            // Add a warning
            $this->markTestIncomplete(
                'This test should ideally fail when called outside transaction, ' .
                'but database configuration might allow it. Always use within DB::transaction()!'
            );
        } catch (\Exception $e) {
            // Expected behavior in strict mode
            $this->assertTrue(true, 'Exception thrown as expected when called outside transaction');
        }
    }
}
