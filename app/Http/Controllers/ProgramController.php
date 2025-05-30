<?php

namespace App\Http\Controllers;

use App;
use App\Models\Partner;
use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $mentors = [
            (object)[
                'name' => 'Anna Kvirikashvili',
                'description' => 'Expert in intelligence operations and cybersecurity with over 20 years of experience in government and private sector.',
                'image' => 'https://robohash.org/mentor1'
            ],
            (object)[
                'name' => 'Levan Toradze',
                'description' => 'Former diplomat and instructor in international relations, specializing in conflict resolution.',
                'image' => 'https://robohash.org/mentor2'
            ],
            (object)[
                'name' => 'Nino Makharadze',
                'description' => 'Trainer in national security policy and law enforcement strategy. Led multiple national seminars.',
                'image' => 'https://robohash.org/mentor3'
            ]
            ];
        $item = Program::findOrFail($id);
        // dd(Program::where('id', $id)->first());
        return view(
            'pages.show-program',
            [
                'item' => $item,
                'language' => App::getLocale(),
                'partners' => Partner::all(),
                'category' => 'programs',
                'mentors' => $mentors
            ]
        );

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
