<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ai\Agents\RecruitmentAssistant;

class AgentController extends Controller
{
    public function invoke(Request $request)
    {
        $validate = $request->validate(['message' => ['required']]);
        $response = RecruitmentAssistant::make(auth('web')->user())
            ->prompt($validate['message']);

        return (string) $response;
    }
}
