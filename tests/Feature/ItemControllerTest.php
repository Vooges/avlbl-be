<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\ItemSize;
use App\Models\Availability;
use App\Models\Role;
use App\Models\UserItemSize;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\assertTrue;

class ItemControllerTest extends TestCase
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

        $values = [
            'In stock',
            'Out of stock',
            'Not retrieved yet',
        ];

        foreach($values as $value){
            Availability::firstOrCreate(['value' => $value]);
        }
                
        $this->item = Item::factory(25)
            ->has(ItemSize::factory(2)
            ->has(UserItemSize::factory(2)->state(new Sequence(
                ['user_id' => $this->user->id],
                ['user_id' => $this->admin->id],
            ))))
        ->create()
        ->first();
    }

    public function test_user_can_view_items(): void
    {
        $url = '/api/items';

        $response = $this->withHeader('Accept', 'application/json')
                         ->actingAs($this->user)
                         ->get($url);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_user_can_view_items_second_page(): void
    {
        $url = '/api/items';

        $response = $this->withHeader('Accept', 'application/json')
                         ->actingAs($this->user)
                         ->get($url);

        $lastPageNumber = $response->json()['meta']['last_page'];

        $response = $this->withHeader('Accept', 'application/json')
                         ->actingAs($this->user)
                         ->get($url.'?page='.$lastPageNumber);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_user_can_search_items(): void
    {
        $searchTerm = substr($this->item->name, 0, 3);

        $url = '/api/items';

        $response = $this->withHeader('Accept', 'application/json')
                         ->actingAs($this->user)
                         ->get($url.'?search='.$searchTerm);

        $response->assertStatus(Response::HTTP_OK);
        $responseData = $response->json()['data'];

        $items = collect($responseData)->pluck('name');

        // * Check if every item contains the searchterm.
        assertTrue($items->every(function (string $value, int $key) use ($searchTerm){
            return str_contains($value, $searchTerm);
        }));
    }

    public function test_user_can_filter_items_by_item_size_ids(): void
    {
        $itemSizeIds = UserItemSize::where('user_id', $this->user->id)->limit(3)->get()->pluck('id');

        $url = '/api/items';
        $queryParams = $itemSizeIds->map(function (int $value, int $key){
            return 'item_size_ids[]='.$value;
        })->join('&');

        $response = $this->withHeader('Accept', 'application/json')
                         ->actingAs($this->user)
                         ->get($url.'?'.$queryParams);

        $response->assertStatus(Response::HTTP_OK);
        $responseData = collect($response->json()['data']);

        // * Check if only items with the relevant itemSizes are present.
        assertTrue($responseData->every(function (array $value, int $key) use ($itemSizeIds){
            return collect($value['itemSizes'])->contains(function (array $value, int $key) use ($itemSizeIds){
                return in_array($value['id'], $itemSizeIds->toArray());
            });
        }));
    }
    
    public function test_user_cannot_create_item(): void
    {
        $url = '/api/items';
        $data = [
            'name' => fake()->word(),
            'image_url' => fake()->url(),
            'colorway' => fake()->word(),
            'store_url' => fake()->url(),
            'item_sizes' => ['40', '40.5'],
        ];

        $response = $this->withHeader('Accept', 'application/json')
                         ->actingAs($this->user)
                         ->post($url, $data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_admin_can_create_item(): void
    {
        $url = '/api/items';
        $data = [
            'name' => fake()->word(),
            'image_url' => fake()->url(),
            'colorway' => fake()->word(),
            'store_url' => fake()->url(),
            'item_sizes' => ['40', '40.5'],
        ];

        $response = $this->withHeader('Accept', 'application/json')
                         ->actingAs($this->admin)
                         ->post($url, $data);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function test_user_can_view_singular_item(): void
    {
        $url = '/api/items/'.$this->item->id;

        $response = $this->withHeader('Accept', 'application/json')
                         ->actingAs($this->user)
                         ->get($url);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_user_cannot_update_item(): void
    {
        $url = '/api/items/'.$this->item->id;
        $data = [
            'name' => fake()->word(),
            'image_url' => fake()->url(),
            'colorway' => fake()->word(),
            'store_url' => fake()->url(),
        ];

        $response = $this->withHeader('Accept', 'application/json')
                         ->actingAs($this->user)
                         ->put($url, $data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_admin_can_update_item(): void
    {
        $url = '/api/items/'.$this->item->id;
        $data = [
            'name' => fake()->word(),
            'image_url' => fake()->url(),
            'colorway' => fake()->word(),
            'store_url' => fake()->url(),
        ];

        $response = $this->withHeader('Accept', 'application/json')
                         ->actingAs($this->admin)
                         ->put($url, $data);
        $responseData = $response->json()['data'];

        $response->assertStatus(Response::HTTP_OK);

        // * name, image_url, colorway and store_url should've changed.
        assertTrue($this->item->name !== $responseData['name']);
        assertTrue($this->item->image_url !== $responseData['image_url']);
        assertTrue($this->item->colorway !== $responseData['colorway']);
        assertTrue($this->item->store_url !== $responseData['store_url']);
    }

    public function test_admin_can_partially_update_item(): void
    {
        $url = '/api/items/'.$this->item->id;
        $data = [
            'name' => fake()->word(),
            'store_url' => fake()->url(),
        ];

        $response = $this->withHeader('Accept', 'application/json')
                         ->actingAs($this->admin)
                         ->put($url, $data);
        $responseData = $response->json()['data'];

        $response->assertStatus(Response::HTTP_OK);

        // * image_url and colorway shouldn't have changed, name and store_url should.
        assertTrue($this->item->name !== $responseData['name']);
        assertTrue($this->item->image_url === $responseData['image_url']);
        assertTrue($this->item->colorway === $responseData['colorway']);
        assertTrue($this->item->store_url !== $responseData['store_url']);
    }

    public function test_user_cannot_delete_item(): void
    {
        $url = '/api/items/'.$this->item->id;

        $response = $this->withHeader('Accept', 'application/json')
                         ->actingAs($this->user)
                         ->delete($url);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_admin_can_delete_item(): void
    {
        $url = '/api/items/'.$this->item->id;

        $response = $this->withHeader('Accept', 'application/json')
                         ->actingAs($this->admin)
                         ->delete($url);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
