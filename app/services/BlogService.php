<?php

namespace App\Services;

use App\Models\BlogPost;
use Illuminate\Support\Str;

class BlogService
{
    public function create(array $data, int $userId): BlogPost
    {
        $data['user_id'] = $userId;
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return BlogPost::create($data);
    }

    public function update(BlogPost $post, array $data): BlogPost
    {
        if ($data['status'] === 'published' && empty($post->published_at)) {
            $data['published_at'] = now();
        }

        $post->update($data);

        return $post;
    }

    public function incrementViews(BlogPost $post): void
    {
        $post->increment('views');
    }

    public function handleImageUpload(array $data, $request): array
    {
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')
                ->store('blog', 'public');
        }

        return $data;
    }
}