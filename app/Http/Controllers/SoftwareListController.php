<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Lab;

class SoftwareListController extends Controller
{
    public function index()
    {
        $labsWithSoftware = Lab::whereNotNull('software_profile_id')
                                ->with('softwareProfile.softwareItems')
                                ->orderBy('lab_name')
                                ->get();

        return view('software-list.index', compact('labsWithSoftware'));
    }
}