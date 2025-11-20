<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prizee;
use Illuminate\Validation\Rule;

class PrizeProbabilitiesController extends Controller
{
    
    public function index(Request $request){
        $prizeedata = Prizee::all();
        $existingTotal = Prizee::sum('probability'); 
        $remaining = 100 - $existingTotal; 
        return view('prizee.index', compact('prizeedata','existingTotal','remaining'));
    }

    public function create(Request $request){
        $existingTotal = Prizee::sum('probability');
        $remaining = 100 - $existingTotal;           

        return view('prizee.create', compact('existingTotal','remaining')); 
    }

    public function store(Request $request){
        $request->validate([
        'title' => ['required','string','max:255', Rule::unique('prizee','title')],
        'probability' => 'required|numeric|min:0.01|max:100',
    ], [
        'title.required' => 'The prize title is required.',
        'title.unique' => 'A prize with this title already exists. Choose a different title.',
        'probability.required' => 'The probability is required.',
        'probability.min' => 'probability must be between 0.01 and 100.',
        'probability.max' => 'probability must be between 0.01 and 100.',
    ]);


    $existingTotal = Prizee::sum('probability');
    $remaining = 100 - $existingTotal;

    
    if ($request->probability > $remaining) {
        return back()
            ->withErrors([
                'probability' => "Only $remaining% remaining. You cannot add {$request->probability}%. Please adjust the probability or delete existing prizes."
            ])
            ->withInput();
    }
    Prizee::create([
        'title' => $request->title,
        'probability' => $request->probability,
    ]);

      return redirect()->route('prizee.index')->with('success', 'Prize created successfully.');
    }

public function edit($id)
{
    try {
        $prizeedata = Prizee::findOrFail($id);
        $existingTotal = Prizee::sum('probability');
        $remaining = 100 - $existingTotal; 
      
        return view('prizee.update', compact('prizeedata', 'existingTotal', 'remaining'));
    } catch (\Exception $e) {
        return redirect()->route('prizee.index')->with('error', 'Prize not found.');
    }
}


public function update(Request $request, $id)
{
    $request->validate([
        'title' => ['required','string','max:255', Rule::unique('prizee','title')->ignore($id)],
        'probability' => 'required|numeric|min:0.01|max:100',
    ], [
        'title.required' => 'The prize title is required.',
        'title.unique' => 'A prize with this title already exists. Choose a different title.',
        'probability.required' => 'The probability is required.',
        'probability.min' => 'probability must be between 0.01 and 100.',
        'probability.max' => 'probability must be between 0.01 and 100.',
    ]);

    try {
        $Prizee = Prizee::findOrFail($id);

        $totalprobability = Prizee::where('id', '!=', $id)->sum('probability');
        $newTotal = $totalprobability + $request->probability;

        if ($newTotal > 100) {
            return back()->withErrors(['probability' => 'Total probability cannot exceed 100%. The sum of all prize probabilitys must be 100% or less.'])->withInput();
        }

        $titleUnchanged = $Prizee->title === $request->title;
        $probabilityUnchanged = (float)$Prizee->probability === (float)$request->probability;
        if ($titleUnchanged && $probabilityUnchanged) {
            return back()->with('info', 'No changes detected.')->withInput();
        }

        $Prizee->title = $request->title;
        $Prizee->probability = $request->probability;
        $Prizee->save();

        return redirect()->route('prizee.index')->with('success', 'Prize updated successfully!');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Error updating prize. Please try again.']);
    }
}


    public function destroy($id)
{
    try {
        Prizee::findOrFail($id)->delete();
        return redirect()->route('prizee.index')->with('success', 'Prize deleted successfully.');
    } catch (\Exception $e) {
        return redirect()->route('prizee.index')->with('error', 'Error deleting prize. Please try again.');
    }
}

}
