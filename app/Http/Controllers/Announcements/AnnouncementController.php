<?php

namespace App\Http\Controllers\Announcements;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
class AnnouncementController extends Controller
{
   // Show the form and list of announcements
   public function index()
   {
       // Fetch all announcements
       $announcements = DB::table('announcements')->get();

       return view('announcements.announcements', compact('announcements'));
   }

   // Store the announcement in the database
   public function store(Request $request)
   {
       // Validate the input
       $request->validate([
           'a_title' => 'required|max:255',
           'a_description' => 'nullable|max:255',
           'a_endpoint' => 'nullable|max:255',
           'a_priority' => 'required|in:Compulsory,Non-Compulsory',
           'a_status' => 'required|in:Enable,Disable'
       ]);

       // Insert the new announcement using the query builder
       DB::table('announcements')->insert([
           'a_title' => $request->a_title,
           'a_description' => $request->a_description,
           'a_endpoint' => $request->a_endpoint,
           'a_priority' => $request->a_priority,
           'a_status' => $request->a_status,
           'a_entry_datetime' => now(),  // Use current timestamp
           'a_entry_user_id' => auth()->user()->id ?? 1,  // Use authenticated user or default to 1 if not available
       ]);

       // Redirect back with a success message
       return redirect()->route('announcements.index')->with('success', 'Announcement added successfully.');
   }

   // Disable an announcement
   public function disable($id)
   {
       DB::table('announcements')->where('a_id', $id)->update(['a_status' => 'Disable']);

       return redirect()->route('announcements.index')->with('success', 'Announcement disabled.');
   }

   // Enable an announcement
   public function enable($id)
   {
       DB::table('announcements')->where('a_id', $id)->update(['a_status' => 'Enable']);

       return redirect()->route('announcements.index')->with('success', 'Announcement enabled.');
   }
}
