<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserItemSizeStoreRequest;
use App\Http\Resources\UserItemSizeResource;
use App\Models\Item;
use App\Models\UserItemSize;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserItemSizeController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(UserItemSize::class, 'userItemSize');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Item $item, UserItemSizeStoreRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id(); 

        $userItemSize = UserItemSize::create($validated);

        return (new UserItemSizeResource($userItemSize))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item, UserItemSize $userItemSize)
    {
        $userItemSize->delete();

        return response()->noContent();
    }
}
