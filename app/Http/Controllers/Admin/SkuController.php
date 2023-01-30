<?php

namespace App\Http\Controllers\Admin;

use App\Actions\ImageActions\DeleteImagesAction;
use App\Actions\ImageActions\SaveImagesAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\SkuRequest;
use App\Http\Resources\SkuCollection;
use App\Http\Resources\SkuResource;
use App\Models\Image;
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
        return new SkuCollection(Sku::with(['product', 'options', 'images'])->paginate(5));
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
        $sku->options()->sync($request->option_id);

        if (!is_null($request->img)) {
//переделать на генератор
            $images = SaveImagesAction::all($request->img);
            foreach ($images as $image) {
                Image::create([
                    'sku_id' => $sku->id,
                    'file' => $image
                ]);
            }
        }
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
        $sku = Sku::with(['product', 'options', 'images'])->findOrFail($sku->id);
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
        $sku->options()->sync($request->option_id);

        if (!is_null($request->img)) {
//переделать на генератор
            $images = SaveImagesAction::all($request->img);
            foreach ($images as $image) {
                Image::create([
                    'sku_id' => $sku->id,
                    'file' => $image
                ]);
            }
        }
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
        $sku->options()->detach();

        DeleteImagesAction::all($sku);

        $sku->delete();

        return response()->noContent();
    }
}
