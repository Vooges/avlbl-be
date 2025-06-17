<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\ItemSize;
use App\Models\Availability;
use App\Models\Role;
use App\Models\UserItemSize;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class ItemSizeControllerTest extends TestCase
{
    use RefreshDatabase;

    private Item $item;
    private User $user;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $userRole = Role::create(['value' => 'user']);
        $adminRole = Role::create(['value' => 'admin']);

        $this->user = User::factory(1)->create([
            'role_id' => $userRole->id
        ])->first();
        $this->admin = User::factory(1)->create([
            'role_id' => $adminRole->id
        ])->first();

        Availability::firstOrCreate(['value' => 'Not retrieved yet']);
                
        $this->item = Item::factory(1)
            ->has(ItemSize::factory(1)
            ->has(UserItemSize::factory(2)->state(new Sequence(
                ['user_id' => $this->user->id],
                ['user_id' => $this->admin->id],
            ))))
        ->create()
        ->first();
    }

    public function test_user_can_view_item_sizes_for_item(): void{
        $url = '/api/items/'.$this->item->id.'/itemSizes';

        $response = $this->withHeader('Accept', 'application/json')->actingAs($this->user)->get($url);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_user_cannot_create_item_size(): void{
        $url = '/api/items/'.$this->item->id.'/itemSizes';
        $data = [
            'value' => '40.5'
        ];

        $response = $this->withHeader('Accept', 'application/json')->actingAs($this->user)->post($url, $data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_admin_cannot_create_item_size_missing_value(): void{
        $url = '/api/items/'.$this->item->id.'/itemSizes';
        $data = [];

        $response = $this->withHeader('Accept', 'application/json')->actingAs($this->admin)->post($url, $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_admin_can_create_item_size(): void{
        $url = '/api/items/'.$this->item->id.'/itemSizes';
        $data = [
            'value' => '40.5'
        ];

        $response = $this->withHeader('Accept', 'application/json')->actingAs($this->admin)->post($url, $data);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function test_user_cannot_delete_item_size(): void{
        $url = '/api/items/'.$this->item->id.'/itemSizes/'.$this->item->itemSizes->first()->id;

        $response = $this->withHeader('Accept', 'application/json')->actingAs($this->user)->delete($url);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_admin_can_delete_item_size(): void{
        $url = '/api/items/'.$this->item->id.'/itemSizes/'.$this->item->itemSizes->first()->id;

        $response = $this->withHeader('Accept', 'application/json')->actingAs($this->admin)->delete($url);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
