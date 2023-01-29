<?php

namespace App\Http\Controllers\Admin;

use App\Actions\CreateImageAction;
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
            foreach ($request->img as $image) {
                $fileName = $image->store('uploads', 'public');
                (new CreateImageAction)([
                    'sku_id' => $sku->id,
                    'file' => $fileName
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
            foreach ($request->img as $image) {
                $fileName = $image->store('uploads', 'public');
                (new CreateImageAction)([
                    'sku_id' => $sku->id,
                    'file' => $fileName
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

        if (!is_null($sku->images)) {
            foreach ($sku->images as $image) {
                if (file_exists($image->file_for_delete)) {
                    unlink($image->file_for_delete);
                }
                $image->delete();
            }
        }

        $sku->delete();

        return response()->noContent();
    }
}
