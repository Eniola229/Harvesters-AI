<?php
// Location: app/Http/Controllers/Admin/NewsletterController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendNewsletterJob;
use App\Models\Campus;
use App\Models\Newsletter;
use App\Models\ChurchMember;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function __construct(protected CloudinaryService $cloudinary) {}

    public function index()
    {
        $newsletters = Newsletter::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.newsletters.index', compact('newsletters'));
    }

    public function create()
    {
        $campuses     = Campus::where('is_active', true)->get();
        $totalMembers = ChurchMember::where('is_active', true)->count();
        return view('admin.newsletters.create', compact('campuses', 'totalMembers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:200',
            'message'       => 'required|string',
            'target_campus' => 'nullable|string',
            'media'         => 'nullable|file|max:51200|mimes:jpg,jpeg,png,gif,mp4,mov,avi',
        ]);

        if ($request->hasFile('media')) {
            $file     = $request->file('media');
            $isVideo  = str_starts_with($file->getMimeType(), 'video');
            $uploaded = $this->cloudinary->upload($file, 'harvesters/newsletters');

            $data['media_url']       = $uploaded['url'];
            $data['media_public_id'] = $uploaded['public_id'];
            $data['media_type']      = $isVideo ? 'video' : 'image';
        }
        unset($data['media']);

        $newsletter = Newsletter::create($data);

        if ($request->has('send_now')) {
            SendNewsletterJob::dispatch($newsletter->id);
            return redirect()->route('admin.newsletters.index')
                ->with('success', 'Newsletter is being sent to all members!');
        }

        return redirect()->route('admin.newsletters.index')->with('success', 'Newsletter saved as draft.');
    }

    public function send(Newsletter $newsletter)
    {
        if ($newsletter->status === 'sent') {
            return back()->with('error', 'This newsletter has already been sent.');
        }

        SendNewsletterJob::dispatch($newsletter->id);

        return redirect()->route('admin.newsletters.index')
            ->with('success', 'Newsletter is being dispatched!');
    }

    public function destroy(Newsletter $newsletter)
    {
        if ($newsletter->media_public_id) {
            $this->cloudinary->delete($newsletter->media_public_id, $newsletter->media_type === 'video' ? 'video' : 'image');
        }
        $newsletter->delete();
        return redirect()->route('admin.newsletters.index')->with('success', 'Newsletter deleted.');
    }
}