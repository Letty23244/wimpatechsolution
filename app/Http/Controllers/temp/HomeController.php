<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Portfolio;
use App\Models\Service;
use App\Models\TeamMember;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function index()
    {
        $services = Service::active()->featured()->orderBy('sort_order')->get();

        $portfolios = Portfolio::published()->featured()
            ->with('service')
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        $testimonials = Testimonial::approved()->featured()
            ->with('service')
            ->latest()
            ->take(6)
            ->get();

        $teamMembers = TeamMember::active()
            ->orderBy('sort_order')
            ->get();

        $latestPosts = BlogPost::published()
            ->with('author')
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('public.home', compact(
            'services',
            'portfolios',
            'testimonials',
            'teamMembers',
            'latestPosts'
        ));
    }
}