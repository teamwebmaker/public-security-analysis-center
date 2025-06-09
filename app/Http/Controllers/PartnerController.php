<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePartnersRequest;
use App\Http\Requests\UpdatePartnersRequest;
use Illuminate\Support\Facades\File;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        return view("admin.partners.index", [
            'partners' => Partner::orderBy('id', 'DESC') -> paginate(6),
            'routeName' => Route::currentRouteName()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.partners.create', [
            'routeName' => Route::currentRouteName()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePartnersRequest $request)
    {
        $data = $request->validated();
        $image = $request -> image;
        $imageName = uniqid() . '-' . time() .'.'. $image -> extension(); // TODO: Generate new File Name
        $uploadPath = 'images/partners/'; //TODO: Set Upload Path
        $image->move(public_path($uploadPath), $imageName); //TODO: Store File in Public Directory
        $title = $data['title'];
        $link = $data['link'];
        Partner::create([
            'title' => $title,
            'link' => $link,
            'image' => $imageName
        ]);
        return redirect() -> route('partners.index') -> with('success', 'პარტნიორი დაემატა წარმატებით');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Partner $partner)
    {
      return view('admin.partners.edit',[
            'partner' => $partner,
            'routeName' => Route::currentRouteName()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePartnersRequest $request, Partner $partner)
    {
        $imageName = null;
        $data = $request->validated();

    if ($request->hasFile('image')) {
        // Delete old image if it exists
        $oldImagePath = public_path('images/partners/' . $partner->image);
        if (File::exists($oldImagePath)) {
            File::delete($oldImagePath);
        }

        // Upload new image
        $image = $request->image;
        $imageName = uniqid() . '-' . time() . '.' . $image->extension();
        $uploadPath = 'images/partners/';
        $image->move(public_path($uploadPath), $imageName);
    }

    // title and ling
     $title = $data['title'];
    $link = $data['link'];

    // Prepare data to update
    $updatedData = [
        'title' => $title,
        'link' => $link,
    ];

    if ($imageName) {
        $updatedData['image'] = $imageName;
    }

    $partner->update($updatedData);

    return redirect()->back()->with('success', 'პარტნიორი განახლდა წარმატებით');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Partner $partner)
    {
        // Delete image from folder if it exists
    $imagePath = public_path('images/partners/' . $partner->image);
    if (File::exists($imagePath)) {
        File::delete($imagePath);
    }

    // Delete the project record
    $partner->delete();

    return redirect()->route('partners.index')->with('success', 'პარტნიორი წარმატებით წაიშალა.');
    }
}
