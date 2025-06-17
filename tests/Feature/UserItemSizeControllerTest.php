<?php

namespace Tests\Feature;

use App\Models\Availability;
use App\Models\Item;
use App\Models\ItemSize;
use App\Models\Role;
use App\Models\User;
use App\Models\UserItemSize;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class UserItemSizeControllerTest extends TestCase
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

        Availability::firstOrCreate(['value' => 'In stock']);
                
        $this->item = Item::factory(1)
            ->has(ItemSize::factory(1)
            ->has(UserItemSize::factory(2)->state(new Sequence(
                ['user_id' => $this->user->id],
                ['user_id' => $this->admin->id],
            ))))
        ->create()
        ->first();
    }
    
    public function test_user_can_store_user_item_size(): void
    {
        $url = '/api/items/'.$this->item->id.'/userItemSizes';
        $data = [
            'item_size_id' => $this->item->itemSizes->first()->id
        ];

        $response = $this->withHeader('Accept', 'application/json')->actingAs($this->user)->post($url, $data);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function test_user_cannot_store_user_item_size_missing_item_size_id(): void
    {
        $url = '/api/items/'.$this->item->id.'/userItemSizes';
        $data = [];

        $response = $this->withHeader('Accept', 'application/json')->actingAs($this->user)->post($url, $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_user_can_delete_own_user_item_size(): void
    {
        $userItemSize = $this->item->itemSizes()->first()
                             ->userItemSizes->where('user_id', $this->user->id)->first();
        $url = '/api/items/'.$this->item->id.'/userItemSizes/'.$userItemSize->id;

        $response = $this->withHeader('Accept', 'application/json')->actingAs($this->user)->delete($url);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_user_cannot_delete_other_users_user_item_size(): void
    {
        $userItemSize = $this->item->itemSizes()->first()
                             ->userItemSizes->where('user_id', '!=', $this->user->id)->first();
        $url = '/api/items/'.$this->item->id.'/userItemSizes/'.$userItemSize->id;

        $response = $this->withHeader('Accept', 'application/json')->actingAs($this->user)->delete($url);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_admin_can_delete_other_users_user_item_size(): void
    {
        $userItemSize = $this->item->itemSizes()->first()
                             ->userItemSizes->where('user_id', '!=', $this->admin->id)->first();
        $url = '/api/items/'.$this->item->id.'/userItemSizes/'.$userItemSize->id;

        $response = $this->withHeader('Accept', 'application/json')->actingAs($this->admin)->delete($url);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
