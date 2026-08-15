<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(
        Request $request,
        DashboardService $dashboard
    ) {
        $period = $request->query(
            'period',
            '30'
        );

        if (
            !in_array(
                $period,
                [
                    '7',
                    '30',
                    '90',
                    'all',
                ],
                true
            )
        ) {
            $period = '30';
        }

        $data = $dashboard->build(
            $period
        );

        return view(
            'admin.dashboard',
            [
                ...$data,
                'period' => $period,
            ]
        );
    }
}