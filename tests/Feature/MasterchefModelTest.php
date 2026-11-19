<?php

namespace Tests\Feature;

use App\Enums\DeliveryStatus;
use App\Enums\OptionGroupType;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Models\Delivery;
use App\Models\Dish;
use App\Models\Notification;
use App\Models\OptionGroup;
use App\Models\OptionValue;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemOption;
use App\Models\Payment;
use App\Models\Review;
use App\Models\User;
use App\Services\PriceCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterchefModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_new_user_is_a_client_by_default(): void
    {
        $user = User::factory()->create();

        $this->assertSame(UserRole::Client, $user->role);
        $this->assertTrue($user->isClient());
        $this->assertFalse($user->isChef());
        $this->assertFalse($user->isLivreur());
        $this->assertFalse($user->isAdmin());
    }

    public function test_factories_can_create_each_role(): void
    {
        $this->assertSame(UserRole::Admin, User::factory()->admin()->create()->role);
        $this->assertSame(UserRole::Chef, User::factory()->chef()->create()->role);
        $this->assertSame(UserRole::Livreur, User::factory()->livreur()->create()->role);
    }

    public function test_catalog_exposes_active_dishes_and_their_options(): void
    {
        $dish = Dish::factory()->create(['base_price' => 8.50]);
        $group = OptionGroup::factory()->for($dish)->create([
            'name' => 'Citron',
            'type' => OptionGroupType::Toggle,
            'is_required' => true,
        ]);
        $value = OptionValue::factory()->for($group, 'group')->create([
            'label' => 'Avec citron',
            'extra_price' => 0.30,
        ]);
        Dish::factory()->inactive()->create();

        $this->assertSame([$dish->id], Dish::active()->pluck('id')->all());
        $this->assertSame(OptionGroupType::Toggle, $group->type);
        $this->assertTrue($group->is_required);
        $this->assertTrue($dish->optionGroups->first()->is($group));
        $this->assertTrue($group->values->first()->is($value));
        $this->assertTrue($value->group->is($group));
    }

    public function test_price_calculator_counts_only_options_of_the_dish(): void
    {
        $dish = Dish::factory()->create(['base_price' => 8.50]);
        $group = OptionGroup::factory()->for($dish)->create(['name' => 'Extras']);
        $cheese = OptionValue::factory()->for($group, 'group')->create([
            'label' => 'Fromage',
            'extra_price' => 1.00,
        ]);
        $bacon = OptionValue::factory()->for($group, 'group')->create([
            'label' => 'Bacon',
            'extra_price' => 1.50,
        ]);

        // Valeur appartenant a un autre plat : elle doit etre ignoree.
        $foreignValue = OptionValue::factory()->create(['extra_price' => 99]);

        $calculator = new PriceCalculator;

        $this->assertSame(2.50, $calculator->extras($dish, [$cheese->id, $bacon->id]));
        $this->assertSame(2.50, $calculator->extras($dish, [$cheese->id, $bacon->id, $foreignValue->id]));
        $this->assertSame(11.00, $calculator->unitPrice($dish, [$cheese->id, $bacon->id]));
        $this->assertSame(22.00, $calculator->total($dish, [$cheese->id, $bacon->id], 2));
        $this->assertSame(0.0, $calculator->total($dish, [], 0));
    }

    public function test_price_calculator_does_not_double_count_a_repeated_value(): void
    {
        $dish = Dish::factory()->create(['base_price' => 10]);
        $group = OptionGroup::factory()->for($dish)->create();
        $value = OptionValue::factory()->for($group, 'group')->create(['extra_price' => 1.25]);

        $calculator = new PriceCalculator;

        $this->assertSame(11.25, $calculator->unitPrice($dish, [$value->id, $value->id]));
    }

    public function test_order_item_options_keep_a_snapshot_of_the_catalog(): void
    {
        $order = Order::factory()->create();
        $dish = Dish::factory()->create();
        $item = OrderItem::create([
            'order_id' => $order->id,
            'dish_id' => $dish->id,
            'quantity' => 1,
            'unit_price' => 8.80,
        ]);

        $snapshot = OrderItemOption::create([
            'order_item_id' => $item->id,
            'group_label' => 'Citron',
            'value_label' => 'Avec citron',
            'extra_price' => 0.30,
        ]);

        $group = OptionGroup::factory()->for($dish)->create(['name' => 'Citron']);
        $catalogValue = OptionValue::factory()->for($group, 'group')->create([
            'label' => 'Avec citron',
            'extra_price' => 0.30,
        ]);

        // L'administrateur change le catalogue apres la commande.
        $catalogValue->update(['label' => 'Citron vert', 'extra_price' => 2.00]);

        $snapshot->refresh();

        $this->assertSame('Citron', $snapshot->group_label);
        $this->assertSame('Avec citron', $snapshot->value_label);
        $this->assertEqualsWithDelta(0.30, (float) $snapshot->extra_price, 0.001);
        $this->assertTrue($item->options->first()->is($snapshot));
    }

    public function test_order_lifecycle_follows_the_readme_state_diagram(): void
    {
        $order = Order::factory()->create(['status' => OrderStatus::Pending]);

        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertTrue($order->canBeCancelled());
        $this->assertTrue(OrderStatus::Pending->canTransitionTo(OrderStatus::Preparing));
        $this->assertTrue(OrderStatus::Pending->canTransitionTo(OrderStatus::Cancelled));
        $this->assertFalse(OrderStatus::Pending->canTransitionTo(OrderStatus::Delivered));

        $order->update(['status' => OrderStatus::Preparing, 'chef_id' => User::factory()->chef()->create()->id]);

        $this->assertFalse($order->fresh()->canBeCancelled());
        $this->assertSame(OrderStatus::Preparing, $order->fresh()->status);
        $this->assertFalse(OrderStatus::Preparing->canTransitionTo(OrderStatus::Delivered));
        $this->assertTrue(OrderStatus::Preparing->canTransitionTo(OrderStatus::Ready));
        $this->assertTrue(OrderStatus::Delivered->isFinal());
    }

    public function test_kitchen_queue_is_sorted_oldest_first(): void
    {
        $older = Order::factory()->create(['status' => OrderStatus::Pending]);
        $older->forceFill(['created_at' => now()->subHours(2)])->saveQuietly();

        $newer = Order::factory()->create(['status' => OrderStatus::Pending]);
        $newer->forceFill(['created_at' => now()->subMinute()])->saveQuietly();

        Order::factory()->create(['status' => OrderStatus::Preparing]);

        $this->assertSame(
            [$older->id, $newer->id],
            Order::queue()->pluck('id')->all()
        );
    }

    public function test_order_total_is_computed_from_its_items(): void
    {
        $order = Order::factory()->create(['total' => 0]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'quantity' => 2,
            'unit_price' => 10.25,
        ]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'quantity' => 1,
            'unit_price' => 4.50,
        ]);

        $this->assertSame(25.0, $order->fresh()->totalFromItems());
    }

    public function test_payment_delivery_review_and_notifications_are_linked_to_the_order(): void
    {
        $client = User::factory()->create();
        $chef = User::factory()->chef()->create();
        $livreur = User::factory()->livreur()->create();

        $order = Order::factory()->create([
            'user_id' => $client->id,
            'chef_id' => $chef->id,
            'status' => OrderStatus::Delivering,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'method' => 'mobile_money',
            'phone' => '+243 970 000 004',
            'amount' => 12.50,
            'status' => PaymentStatus::Paid,
            'reference' => 'MM-TEST-0001',
        ]);

        $delivery = Delivery::create([
            'order_id' => $order->id,
            'livreur_id' => $livreur->id,
            'status' => DeliveryStatus::PickedUp,
            'picked_at' => now(),
        ]);

        $review = Review::create([
            'order_id' => $order->id,
            'user_id' => $client->id,
            'rating' => 5,
            'comment' => 'Exactement comme je voulais.',
        ]);

        $notification = Notification::create([
            'user_id' => $client->id,
            'order_id' => $order->id,
            'type' => 'order_status',
            'message' => 'Votre commande est en route.',
        ]);

        $order->refresh();

        $this->assertTrue($order->user->is($client));
        $this->assertTrue($order->chef->is($chef));
        $this->assertTrue($order->payment->is($payment));
        $this->assertTrue($order->delivery->is($delivery));
        $this->assertTrue($delivery->livreur->is($livreur));
        $this->assertTrue($order->reviews->first()->is($review));
        $this->assertTrue($order->notifications->first()->is($notification));
        $this->assertSame(PaymentStatus::Paid, $payment->status);
        $this->assertSame(DeliveryStatus::PickedUp, $delivery->status);
        $this->assertSame(5, $review->rating);
        $this->assertSame(1, $client->notifications()->unread()->count());
        $this->assertTrue($notification->markAsRead());
        $this->assertSame(0, $client->notifications()->unread()->count());
    }
}
