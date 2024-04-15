<?php

namespace App\Http\Controllers;

use App\Services\RosterService;

class Roster extends Controller
{
    public function index()
    {
        $rosterService = new RosterService();
        $activities = $rosterService->loadDemoRoster();
        echo '<pre>';
        var_dump($activities);
        echo '</pre>';
    }


}
