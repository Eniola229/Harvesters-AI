<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\ChurchMember;
use Illuminate\Http\Request;
use App\Models\Campus;
use App\Models\Conversation;
use Illuminate\Support\Facades\Validator;

class MembersController extends Controller
{
    public function index(Request $request)
    {
        $query = ChurchMember::query();
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }
        if ($request->campus) {
            $query->where('campus', $request->campus);
        }
        if ($request->has('alert')) {
            $query->where('morning_alert', $request->alert === '1');
        }
        $campuses = Campus::all();
        $members = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        return view('admin.members.index', compact('members', 'campuses'));
    }

    public function show(ChurchMember $member)
    {
        $conversations = $member->conversations()
            ->with(['messages' => fn($q) => $q->orderBy('created_at', 'asc')])
            ->latest()
            ->paginate(5);
        return view('admin.members.show', compact('member', 'conversations'));
    }

    public function create()
    {
        $campuses = Campus::all();
        return view('admin.members.create', compact('campuses'));
    }

    public function store(Request $request)
    {
        // Handle bulk upload
        if ($request->hasFile('bulk_file')) {
            return $this->bulkStore($request);
        }

        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'phone'         => ['required', 'string', 'max:20', 'unique:church_members,phone', 'regex:/^\+234[0-9]{10}$/'],
            'campus_id'     => 'nullable|exists:campuses,id',
            'morning_alert' => 'boolean',
            'alert_time'    => 'nullable',
        ], [
            'phone.regex' => 'Phone number must start with +234 followed by 10 digits (e.g. +2348012345678).',
        ]);

        $data['channel'] = $request->channel ?? 'whatsapp';
        ChurchMember::create($data);
        return redirect()->route('admin.members.index')->with('success', 'Member added successfully!');
    }

    protected function bulkStore(Request $request)
    {
        $request->validate([
            'bulk_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file    = $request->file('bulk_file');
        $handle  = fopen($file->getPathname(), 'r');
        $header  = fgetcsv($handle); // skip header row

        $success = 0;
        $skipped = 0;
        $errors  = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 2) continue;

            $name  = trim($row[0]);
            $phone = trim($row[1]);

            // Validate phone
            if (!preg_match('/^\+234[0-9]{10}$/', $phone)) {
                $errors[] = "Skipped \"{$name}\" — phone \"{$phone}\" must start with +234 followed by 10 digits.";
                $skipped++;
                continue;
            }

            // Skip duplicates
            if (ChurchMember::where('phone', $phone)->exists()) {
                $errors[] = "Skipped \"{$name}\" — phone {$phone} already exists.";
                $skipped++;
                continue;
            }

            ChurchMember::create([
                'name'    => ucwords(strtolower($name)),
                'phone'   => $phone,
                'channel' => 'whatsapp',
            ]);
            $success++;
        }

        fclose($handle);

        $message = "{$success} members imported successfully.";
        if ($skipped > 0) $message .= " {$skipped} skipped.";

        return redirect()->route('admin.members.index')
            ->with('success', $message)
            ->with('bulk_errors', $errors);
    }

    public function edit(ChurchMember $member)
    {
        $campuses = Campus::all();
        return view('admin.members.edit', compact('member', 'campuses'));
    }

    public function update(Request $request, ChurchMember $member)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'phone'         => ['required', 'string', 'max:20', 'unique:church_members,phone,' . $member->id, 'regex:/^\+234[0-9]{10}$/'],
            'campus_id'     => 'nullable|exists:campuses,id',
            'morning_alert' => 'boolean',
            'alert_time'    => 'nullable',
            'is_active'     => 'boolean',
        ], [
            'phone.regex' => 'Phone number must start with +234 followed by 10 digits (e.g. +2348012345678).',
        ]);
        $member->update($data);
        return redirect()->route('admin.members.index')->with('success', 'Member updated!');
    }

    public function destroy(ChurchMember $member)
    {
        $member->delete();
        return redirect()->route('admin.members.index')->with('success', 'Member deleted.');
    }
}