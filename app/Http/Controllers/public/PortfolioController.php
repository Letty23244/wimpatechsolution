<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\Service;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function index(Request $request)
    {
        $portfolios = Portfolio::published()
            ->with('service')
            ->when(
                $request->service,
                fn($q) => $q->whereHas('service', fn($q) => $q->where('slug', $request->service))
            )
            ->orderBy('sort_order')
            ->orderByDesc('completed_at')
            ->paginate(12);

        $services = Service::active()->orderBy('name')->get();

        return view('public.portfolio.index', compact('portfolios', 'services'));
    }

    public function show(Portfolio $portfolio)
    {
        abort_if($portfolio->status !== 'published', 404);

        $portfolio->load('service');

        $related = Portfolio::published()
            ->where('service_id', $portfolio->service_id)
            ->where('id', '!=', $portfolio->id)
            ->take(3)
            ->get();

        return view('public.portfolio.show', compact('portfolio', 'related'));
    }
}