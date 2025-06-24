<?php

namespace App\Http\Controllers;

use App\Models\Debate;
use Illuminate\Http\Request;

class DebateController extends Controller
{
    public function index()
    {
        $debates = Debate::with(['creator', 'votes'])->latest()->get();
        return view('debates.index', compact('debates'));
    }
    
    public function vote(Request $request, Debate $debate)
    {
        $request->validate(['vote' => 'required|in:a,b']);
        
        // Check if already voted
        if ($debate->votes()->where('user_id', auth()->id())->exists()) {
            return back()->with('error', 'Already voted!');
        }
        
        $debate->votes()->create([
            'user_id' => auth()->id(),
            'vote' => $request->vote,
        ]);
        
        // Award credits
        auth()->user()->addCredits(2);
        
        return back()->with('success', 'Vote recorded! +2 credits');
    }
}