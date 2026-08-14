<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function show(Request $request, string $slug): View
    {
        /** @var array{title: string, description: string} $catalog */
        $catalog = $request->route('catalog');

        return view('pages.detail', [
            'catalogTitle' => $catalog['title'],
            'catalogDescription' => $catalog['description'],
            'routePrefix' => $request->route('routePrefix'),
            'slug' => $slug,
        ]);
    }
}
