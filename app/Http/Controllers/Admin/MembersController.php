<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChurchMember;
use Illuminate\Http\Request;
use App\Models\Campus;
use App\Models\Conversation;

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
        $member->load(['conversations.messages' => fn($q) => $q->orderBy('created_at', 'desc')->take(20)]);
        $conversations = Conversation::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.members.show', compact('member', 'conversations')); 
    }

    public function create()
    {
        $campuses = Campus::all();

        return view('admin.members.create', compact('campuses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'phone'         => 'required|string|max:20|unique:church_members,phone',
            'campus'        => 'nullable|string|max:100',
            'morning_alert' => 'boolean',
            'alert_time'    => 'nullable',
        ]);

        ChurchMember::create($data);

        return redirect()->route('admin.members.index')->with('success', 'Member added successfully!');
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
            'phone'         => 'required|string|max:20|unique:church_members,phone,' . $member->id,
            'campus'        => 'nullable|string|max:100',
            'morning_alert' => 'boolean',
            'alert_time'    => 'nullable',
            'is_active'     => 'boolean',
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