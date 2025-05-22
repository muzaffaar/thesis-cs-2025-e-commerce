<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CatalogStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        //$locales = config('locales.supported_locales');
        return [
            'name' => 'required|string',
            'description' => 'required|string',
            'slug' => 'required|string|unique:catalogs,slug',
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
        $locales = config('locales.supported_locales'); // dynamic!

        $validator->after(function ($validator) use ($locales) {
            $translations = $this->input('translations', []);

            // Check if all required locales are present
            $submittedLocales = collect($translations)->pluck('locale')->all();
            $missingLocales = array_diff($locales, $submittedLocales);

            foreach ($missingLocales as $missing) {
                $validator->errors()->add("translations", __("validation.locale_required", ['locale' => $missing]));
            }

            // Validate each submitted translation
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

                // Unique slug per locale
                $exists = \DB::table('catalog_translations')
                    ->where('locale', $locale)
                    ->where('slug', $slug)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add("translations.$index.slug", __("validation.unique", ['attribute' => "translations.$index.slug"]));
                }
            }
        });
    }
}
