<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\CatalogStoreRequest;
use App\Http\Requests\CatalogUpdateRequest;
use App\Models\Catalog;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CatalogController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function store(CatalogStoreRequest $request): JsonResponse
    {
        $this->authorize('create', Catalog::class);
        try {
            \DB::transaction(function () use ($request) {
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
                            'url' => $path,
                            'alt_text' => $request->input('alt_text.' . $index, ''),
                            'sort_order' => $index,
                        ]);
                    }
                }
            });

            $catalog = Catalog::latest()->first();

            return response()->json([
                'message' => __('success_response.catalog_created'),
                'catalog' => $catalog->load('translations', 'images', 'children', 'parent'),
            ]);
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(CatalogUpdateRequest $request, string $locale, string $catalog): JsonResponse
    {
        try {
            $catalog = Catalog::where('slug', $catalog)->firstOrFail();

            $this->authorize('update', $catalog);

            \DB::transaction(function () use ($request, $catalog) {
                $catalog->update([
                    'name' => $request->get('name'),
                    'description' => $request->get('description'),
                    'slug' => $request->get('slug'),
                    'is_active' => $request->get('is_active', true),
                    'parent_id' => $request->get('parent_id'),
                ]);

                $incomingLocales = collect($request->get('translations'))->pluck('locale')->toArray();
                $catalog->translations()->whereNotIn('locale', $incomingLocales)->delete();

                foreach ($request->get('translations') as $translation) {
                    $catalog->translations()->updateOrCreate(
                        ['locale' => $translation['locale']],
                        [
                            'name' => $translation['name'],
                            'slug' => $translation['slug'],
                        ]
                    );
                }

                if ($request->hasFile('images')) {

                    foreach ($catalog->images as $image) {
                        Storage::disk('public')->delete($image->url);
                        $image->delete();
                    }

                    foreach ($request->file('images') as $index => $imageFile) {
                        $path = $imageFile->store('catalog-images', 'public');
                        $catalog->images()->create([
                            'url' => $path,
                            'alt_text' => $request->input('alt_text.' . $index, ''),
                            'sort_order' => $index,
                        ]);
                    }
                }
            });

            return response()->json([
                'message' => __('success_response.catalog_updated'),
                'catalog' => $catalog->load('translations', 'images', 'children', 'parent')
            ]);
        } catch (ModelNotFoundException $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => $e->getMessage()], 404);

        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => __('error_responses.catalog_update_error')], 500);
        }
    }

    public function destroy(string $locale, string $slug): JsonResponse
    {
        try {
            $catalog = Catalog::where('slug', $slug)->firstOrFail();

            $this->authorize('delete', $catalog);

            \DB::transaction(function () use ($catalog) {

                foreach ($catalog->images as $image) {
                    Storage::disk('public')->delete($image->url);
                    $image->delete();
                }

                $catalog->translations()->delete();

                $catalog->children()->update(['parent_id' => null]);

                $catalog->delete();
            });

            return response()->json([
                'message' => __('success_response.catalog_deleted'),
            ]);
        } catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @throws AuthorizationException
     */
    public function show(string $locale, string $slug): JsonResponse
    {
        try {
            $catalog = Catalog::where('slug', $slug)->firstOrFail();

            $this->authorize('view', $catalog);

            $catalog->load('translation', 'images', 'children.translation', 'parent.translation');

            return response()->json([
                'catalog' => $catalog,
            ]);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    /**
     * @throws AuthorizationException
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Catalog::class);
        try {
            $catalogs = Catalog::with([
                'translation',
                'images',
                'children.translation',
            ])->get();


            return response()->json([
                'catalogs' => $catalogs,
            ]);
        } catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
