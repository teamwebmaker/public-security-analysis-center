<?php

namespace App\Http\Controllers;


use App\Http\Requests\StoreProgramsRequest;
use App\Models\Partner;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;

class ProgramController  extends CrudController
{

  protected string $modelClass = Program::class;
    protected string $contextField = 'program';
    protected string $viewFolder = 'programs';
    protected string $uploadPath = 'images/programs/';

   protected array $imageFields = [
    'image' => 'images/programs/',
    'certificate_image' => 'images/certificates/',
];
    /**
     * Display a listing of the resource.
     */
 

    /**
     * Show the form for creating a new resource.
     */


    /**
     * Store a newly created resource in storage.
     */
   public function store(StoreProgramsRequest $request)
{
    $data = $request->validated();


      foreach ($this->imageFields as $field => $path) {
            $data[$field] = $this->handleImageUpload($request, $field);
        }

    $programData = [
        'title' => $data['title'],
        'description' => $data['description'],
        'image' => $data['image'],
        'certificate_image' => $data['certificate_image'],
        'video' => $data['video'],
        'price' => $data['price'],
        'duration' => $data['duration'],
        'address' => $data['address'],
        'start_date' => $data['start_date'],
        'end_date' => $data['end_date'],
        'start_time' => $data['hour']['start'],
        'end_time' => $data['hour']['end'],
        'days' => json_encode($data['days']),
        'visibility' => $data['visibility'],
    ];
    dd($programData);
    Program::create($programData);

    return redirect()->route('programs.index')->with('success', 'პროგრამა შეიქმნა წარმატებით');
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = Program::with(['syllabuses', 'mentors'])->findOrFail($id);
        // dd(vars: );
        return view(
            'pages.show-program',
            [
                'item' => $item,
                'language' => App::getLocale(),
                'partners' => Partner::all(),
                'category' => 'programs',
                // 'mentors' => $mentors
            ]
        );
    }
 

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }
 
}
