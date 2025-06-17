<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemStoreRequest;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ItemResource;
use App\Http\Requests\ItemIndexRequest;
use App\Http\Requests\ItemUpdateRequest;
use App\Models\Availability;
use App\Models\ItemSize;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;


class ItemController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Item::class, 'item');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ItemIndexRequest $request): AnonymousResourceCollection
    {
        $validated = $request->validated();

        $userId = Auth::id();

        // * Filter to only show the items tracked by the user.
        $itemsQuery = Item::whereHas('itemSizes.userItemSizes', function ($q) use ($userId){
            $q->where('user_id', $userId);
        })
        ->with([
            'itemSizes' => function ($query) use ($userId) {
                $query->whereHas('userItemSizes', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })->with([
                    'availability',
                    'userItemSizes' => function ($q) use ($userId) {
                        $q->where('user_id', $userId);
                    }
                ]);
            }
        ]);

        if (isset($validated['search']) && $search = $validated['search']){
            $itemsQuery = $itemsQuery->where('name', 'LIKE', "%$search%");
        }

        if (isset($validated['item_size_ids']) && $itemSizeIds = $validated['item_size_ids']){
            $itemsQuery = $itemsQuery->whereHas('itemSizes', function ($q) use ($itemSizeIds){
                $q->whereIn('id', $itemSizeIds);
            });
        }

        return ItemResource::collection($itemsQuery->paginate(20));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ItemStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $item = Item::create($validated);
        $availability = Availability::where('value', 'Not retrieved yet')->first();

        foreach($validated['item_sizes'] as $itemSize){
            ItemSize::create([
                'value' => $itemSize,
                'item_id' => $item->id,
                'availability_id' => $availability->id,
            ]);
        }

        return (new ItemResource($item->load('itemSizes.availability')))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item): ItemResource
    {
        return new ItemResource($item->load('itemSizes.availability'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ItemUpdateRequest $request, Item $item): ItemResource
    {
        $validated = $request->validated();

        foreach ($validated as $key => $value){
            if (!empty($value)) $item->{$key} = $value;
        }

        $item->save();

        return new ItemResource($item->load('itemSizes.availability'));
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item): Response
    {
        $item->delete(); 
        
        return response()->noContent();
    }
}
