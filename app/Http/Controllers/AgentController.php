<?php

namespace App\Http\Controllers;

use App\Ai\Agents\RecruitmentAssistant;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function invoke(Request $request)
    {
        $agent = RecruitmentAssistant::make();
        $data = $request->validate(['message' => ['required']]);
        $response = RecruitmentAssistant::make(auth('web')->user())
            ->prompt($data['message']);

        return (string) $response;
    }
}
