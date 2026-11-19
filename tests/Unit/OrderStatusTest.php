<?php

namespace Tests\Unit;

use App\Enums\OrderStatus;
use PHPUnit\Framework\TestCase;

class OrderStatusTest extends TestCase
{
    public function test_the_happy_path_is_the_only_forward_path(): void
    {
        $this->assertTrue(OrderStatus::Pending->canTransitionTo(OrderStatus::Preparing));
        $this->assertTrue(OrderStatus::Preparing->canTransitionTo(OrderStatus::Ready));
        $this->assertTrue(OrderStatus::Ready->canTransitionTo(OrderStatus::Delivering));
        $this->assertTrue(OrderStatus::Delivering->canTransitionTo(OrderStatus::Delivered));
        $this->assertTrue(OrderStatus::Delivered->isFinal());
    }

    public function test_only_a_pending_order_can_be_cancelled(): void
    {
        $this->assertTrue(OrderStatus::Pending->canBeCancelled());
        $this->assertTrue(OrderStatus::Pending->canTransitionTo(OrderStatus::Cancelled));
        $this->assertFalse(OrderStatus::Preparing->canBeCancelled());
        $this->assertFalse(OrderStatus::Cancelled->canTransitionTo(OrderStatus::Preparing));
    }

    public function test_statuses_cannot_skip_a_step(): void
    {
        $this->assertFalse(OrderStatus::Pending->canTransitionTo(OrderStatus::Ready));
        $this->assertFalse(OrderStatus::Preparing->canTransitionTo(OrderStatus::Delivering));
        $this->assertFalse(OrderStatus::Ready->canTransitionTo(OrderStatus::Delivered));
        $this->assertFalse(OrderStatus::Cancelled->canTransitionTo(OrderStatus::Delivered));
    }

    public function test_labels_are_in_french(): void
    {
        $this->assertSame('En attente', OrderStatus::Pending->label());
        $this->assertSame('En préparation', OrderStatus::Preparing->label());
        $this->assertSame('Prête', OrderStatus::Ready->label());
        $this->assertSame('En livraison', OrderStatus::Delivering->label());
        $this->assertSame('Livrée', OrderStatus::Delivered->label());
        $this->assertSame('Annulée', OrderStatus::Cancelled->label());
    }
}
