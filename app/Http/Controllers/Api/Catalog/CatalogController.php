<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\CatalogStoreRequest;
use App\Models\Catalog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CatalogController extends Controller
{
    /** This method creates new category
     * @param CatalogStoreRequest $request validates incoming data from user
     * @return \Illuminate\Http\JsonResponse including category data and relations e.g, parent, children, images, ...
     */
    public function store(CatalogStoreRequest $request) {
        // TODO: POLICY must be implemented in order to avoid unauthorized access
        try {
            $catalog = Catalog::create([
                'name' => $request->get('name'),
                'description' => $request->get('description'),
                'slug' => $request->get('slug') ?? Str::slug($request->get('name')),
                'is_active' => $request->get('is_active') ?? true,
                'parent_id' => $request->get('parent_id') ?? null,
            ]);

            foreach ($request->get('translations') as $translation) {
                $catalog->translations()->create([
                    'locale' => $translation['locale'],
                    'name' => $translation['name'],
                    'slug' => $translation['slug'],
                ]);
            }

            // TODO: manipulate image size, quality according to requirements
            // TODO: try to use "composer require intervention/image" package
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $imageFile) {
                    $path = $imageFile->store('catalog-images', 'public');

                    $catalog->images()->create([
                        'url' => Storage::disk('public')->url($path),
                        'alt_text' => $request->input('alt_text.' . $index, ''),
                        'sort_order' => $index,
                    ]);
                }
            }

            return response()->json([
                'message' => __('success_response.catalog_created'),
                'catalog' => $catalog->load('translations', 'images', 'children', 'parent'),
            ]);
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
