<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\Catalog;

class CatalogUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $catalog = $this->getCatalogFromSlug();

        return [
            'name' => 'required|string',
            'description' => 'required|string',
            'slug' => [
                'required',
                'string',
                Rule::unique('catalogs', 'slug')->ignore($catalog?->id),
            ],
            'is_active' => 'boolean',
            'parent_id' => 'integer|nullable|exists:catalogs,id',

            'images' => 'sometimes|array',
            'images.*' => 'file|image|max:2048',

            'translations' => ['required', 'array', 'min:1'],
            'translations.*.locale' => 'required|string',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.slug' => 'required|string|max:255',
        ];
    }

    public function withValidator($validator)
    {
        $locales = config('locales.supported_locales');
        $translations = $this->input('translations', []);
        $catalog = $this->getCatalogFromSlug();
        $catalogId = $catalog?->id;

        $validator->after(function ($validator) use ($locales, $translations, $catalogId) {
            $submittedLocales = collect($translations)->pluck('locale')->all();
            $missingLocales = array_diff($locales, $submittedLocales);

            foreach ($missingLocales as $missing) {
                $validator->errors()->add("translations", __("validation.locale_required", ['locale' => $missing]));
            }

            foreach ($translations as $index => $trans) {
                $locale = $trans['locale'] ?? null;
                $slug = $trans['slug'] ?? null;

                if (!$locale || !in_array($locale, $locales)) {
                    $validator->errors()->add("translations.$index.locale", __("validation.invalid_locale", ['locale' => $locale]));
                }

                if (!$slug) {
                    $validator->errors()->add("translations.$index.slug", __("validation.required", ['attribute' => "translations.$index.slug"]));
                    continue;
                }

                $exists = DB::table('catalog_translations')
                    ->where('locale', $locale)
                    ->where('slug', $slug)
                    ->when($catalogId, function ($query) use ($catalogId) {
                        return $query->where('catalog_id', '!=', $catalogId);
                    })
                    ->exists();

                if ($exists) {
                    $validator->errors()->add("translations.$index.slug", __("validation.unique", ['attribute' => "translations.$index.slug"]));
                }
            }
        });
    }

    /**
     * Get the catalog model from the route slug.
     */
    protected function getCatalogFromSlug(): ?Catalog
    {
        $slug = $this->route('catalog');
        return Catalog::where('slug', $slug)->first();
    }
}
