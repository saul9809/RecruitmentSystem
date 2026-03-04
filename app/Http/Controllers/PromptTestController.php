<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PromptTestController extends Controller
{
    public function testAgent(Request $request)
    {
        return response()->json([
            'message' => 'Test successful',
            'input' => $request->all(),
        ]);
    }
}
