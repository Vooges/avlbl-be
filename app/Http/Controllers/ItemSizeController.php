<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemSizeStoreRequest;
use App\Http\Resources\ItemSizeResource;
use App\Models\Availability;
use App\Models\Item;
use App\Models\ItemSize;
use Illuminate\Http\Response;

class ItemSizeController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ItemSize::class, 'itemSize');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Item $item)
    {
        $itemSizes = ItemSize::where('item_id', $item->id)->with('availability')->get();

        return ItemSizeResource::collection($itemSizes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Item $item, ItemSizeStoreRequest $request)
    {
        $validated = $request->validated();
        $validated['item_id'] = $item->id;
        $validated['availability_id'] = Availability::where('value', 'Not retrieved yet')->first()->id;

        $itemSize = ItemSize::create($validated);

        return (new ItemSizeResource($itemSize->load('availability')))->response()->setStatusCode(Response::HTTP_CREATED);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item, ItemSize $itemSize)
    {
        $itemSize->delete();

        return response()->noContent();
    }
}
