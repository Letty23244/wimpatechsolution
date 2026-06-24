<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::active()
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        return view('public.services.index', compact('services'));
    }

    public function show(Service $service)
    {
        abort_if($service->status !== 'active', 404);

        $service->load(['portfolios' => fn($q) => $q->published()->take(4)]);

        $relatedServices = Service::active()
            ->where('category', $service->category)
            ->where('id', '!=', $service->id)
            ->take(3)
            ->get();

        return view('public.services.show', compact('service', 'relatedServices'));
    }
}