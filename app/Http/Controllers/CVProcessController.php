<?php

namespace App\Http\Controllers;


use Inertia\Inertia;
use Illuminate\Http\Request;

class CVProcessController extends Controller
{

    public function index()
    {
        return Inertia::render('CVProcess/Index');
    }
    public function store(Request $request)
    {
        // Lógica para almacenar un nuevo proceso de CV
    }
}
