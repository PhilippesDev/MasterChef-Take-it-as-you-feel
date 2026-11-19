<?php

namespace Tests\Feature;

use App\Models\Dish;
use App\Models\OptionGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MasterchefSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeders_create_the_four_accounts_and_the_catalog(): void
    {
        $this->seed();

        $this->assertDatabaseCount('users', 4);
        $this->assertDatabaseHas('users', ['email' => 'admin@masterchef.test', 'role' => 'admin']);
        $this->assertDatabaseHas('users', ['email' => 'chef@masterchef.test', 'role' => 'chef']);
        $this->assertDatabaseHas('users', ['email' => 'livreur@masterchef.test', 'role' => 'livreur']);
        $this->assertDatabaseHas('users', ['email' => 'client@masterchef.test', 'role' => 'client']);

        $client = User::where('email', 'client@masterchef.test')->firstOrFail();
        $this->assertTrue(Hash::check('password', $client->password));

        $this->assertDatabaseCount('dishes', 3);
        $this->assertDatabaseHas('dishes', ['name' => 'Burger Masterchef', 'base_price' => 8.50]);
        $this->assertDatabaseHas('dishes', ['name' => 'Poulet braisé', 'base_price' => 7.00]);
        $this->assertDatabaseHas('dishes', ['name' => 'Wrap végétarien', 'base_price' => 6.00]);

        $burger = Dish::where('name', 'Burger Masterchef')->firstOrFail();
        $this->assertTrue($burger->optionGroups()->where('name', 'Citron')->exists());
        $this->assertTrue($burger->optionGroups()->where('name', 'Piquant')->exists());
        $this->assertSame(7, $burger->optionGroups()->count());

        $citron = OptionGroup::where('dish_id', $burger->id)->where('name', 'Citron')->firstOrFail();
        $this->assertTrue($citron->is_required);
        $this->assertSame(2, $citron->values()->count());
    }

    public function test_seeders_are_idempotent(): void
    {
        $this->seed();
        $this->seed();

        $this->assertSame(4, User::count());
        $this->assertSame(3, Dish::count());
        $this->assertSame(7, OptionGroup::whereHas('dish', fn ($query) => $query->where('name', 'Burger Masterchef'))->count());
    }
}
