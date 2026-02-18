<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChurchMember;
use App\Models\Conversation;
use App\Models\Newsletter;
use App\Models\Program;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_members'    => ChurchMember::count(),
            'active_members'   => ChurchMember::where('is_active', true)->count(),
            'alert_members'    => ChurchMember::where('morning_alert', true)->count(),
            'total_programs'   => Program::count(),
            'upcoming_programs'=> Program::upcoming()->count(),
            'newsletters_sent' => Newsletter::where('status', 'sent')->count(),
            'total_messages'   => Conversation::count(),
            'today_messages'   => Conversation::whereDate('updated_at', today())->count(),
        ];

        $recentMembers  = ChurchMember::orderBy('created_at', 'desc')->take(5)->get();
        $upcomingEvents = Program::upcoming()->orderBy('start_date')->take(5)->get();
        $recentNewsletters = Newsletter::orderBy('created_at', 'desc')->take(3)->get();

        return view('admin.dashboard', compact('stats', 'recentMembers', 'upcomingEvents', 'recentNewsletters'));
    }
}