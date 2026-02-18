<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\Leader;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;

class LeaderController extends Controller
{
    public function __construct(protected CloudinaryService $cloudinary) {}

    public function index()
    {
        $leaders = Leader::with('campus')->orderBy('order')->get();
        return view('admin.leaders.index', compact('leaders'));
    }

    public function create()
    {
        $campuses = Campus::where('is_active', true)->get();
        return view('admin.leaders.create', compact('campuses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'title'     => 'nullable|string|max:100',
            'phone'     => 'nullable|string|max:20',
            'email'     => 'nullable|email',
            'bio'       => 'nullable|string',
            'campus_id' => 'nullable|exists:campuses,id',
            'order'     => 'nullable|integer',
            'is_active' => 'boolean',
            'photo'     => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            $uploaded = $this->cloudinary->upload($request->file('photo'), 'harvesters/leaders');
            $data['image_url'] = $uploaded['url'];
        }

        unset($data['photo']);
        $data['is_active'] = $request->boolean('is_active', true);

        Leader::create($data);

        return redirect()->route('admin.leaders.index')->with('success', 'Leader added!');
    }

    public function edit(Leader $leader)
    {
        $campuses = Campus::where('is_active', true)->get();
        return view('admin.leaders.edit', compact('leader', 'campuses'));
    }

    public function update(Request $request, Leader $leader)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'title'     => 'nullable|string|max:100',
            'phone'     => 'nullable|string|max:20',
            'email'     => 'nullable|email',
            'bio'       => 'nullable|string',
            'campus_id' => 'nullable|exists:campuses,id',
            'order'     => 'nullable|integer',
            'is_active' => 'boolean',
            'photo'     => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            $uploaded = $this->cloudinary->upload($request->file('photo'), 'harvesters/leaders');
            $data['image_url'] = $uploaded['url'];
        }

        unset($data['photo']);
        $data['is_active'] = $request->boolean('is_active', true);

        $leader->update($data);

        return redirect()->route('admin.leaders.index')->with('success', 'Leader updated!');
    }

    public function destroy(Leader $leader)
    {
        $leader->delete();
        return redirect()->route('admin.leaders.index')->with('success', 'Leader deleted.');
    }
}