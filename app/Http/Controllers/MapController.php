<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        $apiKey = config('services.maptiler.key');
        return view('map.index', compact('apiKey'));
    }
}