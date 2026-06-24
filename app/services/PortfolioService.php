<?php

namespace App\Services;

use App\Models\Portfolio;
use Illuminate\Support\Str;

class PortfolioService
{
    public function create(array $data): Portfolio
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        return Portfolio::create($data);
    }

    public function update(Portfolio $portfolio, array $data): Portfolio
    {
        $portfolio->update($data);

        return $portfolio;
    }

    public function handleImageUploads(array $data, $request): array
    {
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')
                ->store('portfolios', 'public');
        }

        if ($request->hasFile('gallery')) {
            $data['gallery'] = collect($request->file('gallery'))
                ->map(fn($file) => $file->store('portfolios/gallery', 'public'))
                ->toArray();
        }

        return $data;
    }
}