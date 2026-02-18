<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;

class ProgramsController extends Controller
{
    public function __construct(protected CloudinaryService $cloudinary) {}

    public function index()
    {
        $programs = Program::orderBy('start_date', 'desc')->paginate(15);
        return view('admin.programs.index', compact('programs'));
    }

    public function create()
    {
        return view('admin.programs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:200',
            'description' => 'required|string',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date',
            'venue'       => 'nullable|string|max:200',
            'campus'      => 'nullable|string|max:100',
            'is_active'   => 'boolean',
            'is_featured' => 'boolean',
            'image'       => 'nullable|image|max:10240',
            // Metadata
            'meta_bus_locations'  => 'nullable|string',
            'meta_free_meal'      => 'nullable|string',
            'meta_dress_code'     => 'nullable|string',
            'meta_registration'   => 'nullable|string',
            'meta_contact'        => 'nullable|string',
            'meta_extra'          => 'nullable|string',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $uploaded = $this->cloudinary->upload($request->file('image'), 'harvesters/programs');
            $data['image_url']       = $uploaded['url'];
            $data['image_public_id'] = $uploaded['public_id'];
        }
        unset($data['image']);

        // Build metadata
        $metadata = [];
        foreach (['bus_locations', 'free_meal', 'dress_code', 'registration', 'contact', 'extra'] as $key) {
            $value = $data["meta_{$key}"] ?? null;
            if ($value) $metadata[$key] = $value;
            unset($data["meta_{$key}"]);
        }
        $data['metadata'] = $metadata ?: null;

        Program::create($data);

        return redirect()->route('admin.programs.index')->with('success', 'Program created successfully!');
    }

    public function edit(Program $program)
    {
        return view('admin.programs.edit', compact('program'));
    }

    public function update(Request $request, Program $program)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:200',
            'description' => 'required|string',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date',
            'venue'       => 'nullable|string|max:200',
            'campus'      => 'nullable|string|max:100',
            'is_active'   => 'boolean',
            'is_featured' => 'boolean',
            'image'       => 'nullable|image|max:10240',
            'meta_bus_locations'  => 'nullable|string',
            'meta_free_meal'      => 'nullable|string',
            'meta_dress_code'     => 'nullable|string',
            'meta_registration'   => 'nullable|string',
            'meta_contact'        => 'nullable|string',
            'meta_extra'          => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($program->image_public_id) {
                $this->cloudinary->delete($program->image_public_id);
            }
            $uploaded = $this->cloudinary->upload($request->file('image'), 'harvesters/programs');
            $data['image_url']       = $uploaded['url'];
            $data['image_public_id'] = $uploaded['public_id'];
        }
        unset($data['image']);

        $metadata = [];
        foreach (['bus_locations', 'free_meal', 'dress_code', 'registration', 'contact', 'extra'] as $key) {
            $value = $data["meta_{$key}"] ?? null;
            if ($value) $metadata[$key] = $value;
            unset($data["meta_{$key}"]);
        }
        $data['metadata'] = $metadata ?: null;

        $program->update($data);

        return redirect()->route('admin.programs.index')->with('success', 'Program updated!');
    }

    public function destroy(Program $program)
    {
        if ($program->image_public_id) {
            $this->cloudinary->delete($program->image_public_id);
        }
        $program->delete();
        return redirect()->route('admin.programs.index')->with('success', 'Program deleted.');
    }
}