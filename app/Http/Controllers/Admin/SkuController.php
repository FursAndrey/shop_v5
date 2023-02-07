<?php

namespace App\Http\Controllers\Admin;

use App\Actions\SkuActions\DeleteSkuAction;
use App\Actions\SkuActions\SaveSkuAttributesAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\SkuRequest;
use App\Http\Resources\SkuCollection;
use App\Http\Resources\SkuResource;
use App\Models\Sku;

class SkuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new SkuCollection(Sku::with(['product', 'product.properties', 'options', 'options.property', 'images'])->paginate(5));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SkuRequest $request)
    {
        $sku = Sku::create($request->validated());
        (new SaveSkuAttributesAction)($request, $sku);

        return new SkuResource($sku);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sku  $sku
     * @return \Illuminate\Http\Response
     */
    public function show(Sku $sku)
    {
        $sku = Sku::with(['product', 'product.properties', 'options', 'options.property', 'images'])->findOrFail($sku->id);
        return new SkuResource($sku);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sku  $sku
     * @return \Illuminate\Http\Response
     */
    public function update(SkuRequest $request, Sku $sku)
    {
        $sku->update($request->validated());
        (new SaveSkuAttributesAction)($request, $sku);
        
        return new SkuResource($sku);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sku  $sku
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sku $sku)
    {
        (new DeleteSkuAction)($sku);

        return response()->noContent();
    }
}
