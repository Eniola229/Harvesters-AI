<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;

class CampusController extends Controller
{
    public function __construct(protected CloudinaryService $cloudinary) {}

    public function index()
    {
        $campuses = Campus::withCount('leaders')->orderBy('name')->get();
        return view('admin.campuses.index', compact('campuses'));
    }

    public function create()
    {
        return view('admin.campuses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'address'       => 'required|string|max:255',
            'city'          => 'nullable|string|max:100',
            'state'         => 'nullable|string|max:100',
            'country'       => 'nullable|string|max:100',
            'pastor_name'   => 'nullable|string|max:100',
            'pastor_phone'  => 'nullable|string|max:20',
            'service_times' => 'nullable|string|max:255',
            'image'         => 'nullable|image|max:5120',
            'is_active'     => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $uploaded = $this->cloudinary->upload($request->file('image'), 'harvesters/campuses');
            $data['image_url'] = $uploaded['url'];
        }
        unset($data['image']);

        Campus::create($data);
        return redirect()->route('admin.campuses.index')->with('success', 'Campus added!');
    }

    public function edit(Campus $campus)
    {
        return view('admin.campuses.edit', compact('campus'));
    }

    public function update(Request $request, Campus $campus)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'address'       => 'required|string|max:255',
            'city'          => 'nullable|string|max:100',
            'state'         => 'nullable|string|max:100',
            'country'       => 'nullable|string|max:100',
            'pastor_name'   => 'nullable|string|max:100',
            'pastor_phone'  => 'nullable|string|max:20',
            'service_times' => 'nullable|string|max:255',
            'image'         => 'nullable|image|max:5120',
            'is_active'     => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $uploaded = $this->cloudinary->upload($request->file('image'), 'harvesters/campuses');
            $data['image_url'] = $uploaded['url'];
        }
        unset($data['image']);

        $campus->update($data);
        return redirect()->route('admin.campuses.index')->with('success', 'Campus updated!');
    }

    public function destroy(Campus $campus)
    {
        $campus->delete();
        return redirect()->route('admin.campuses.index')->with('success', 'Campus deleted.');
    }
}