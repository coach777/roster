<?php

namespace App\Services;

use App\Interfaces\RosterSerializer;
use App\Models\Activity;
use App\Serializers\rosterBuster;
use stringEncode\Exception;

class RosterService
{

    private function getRosterActivities( string $source ){
        $serializer = $this->discoverSerializerForRoster($source);
        $activities = $serializer->parseRoster($source);
        return $activities;
    }

    private function discoverSerializerForRoster( string $roster ) : ?rosterSerializer
    {

        if(  strpos($roster, 'https://air-traffic-control.rosterbuster.aero/rosterbuster/')  ){
            return new rosterBuster();
        }

        abort(422, 'Invalid roster: format not recognized.');
        return null;
    }


    /**
     * Loads a demo roster from a provided example file
     *
     * @return array
     */
    public function loadDemoRoster()
    {
        $roster_html = file_get_contents( __DIR__.'/../../resources/roster.html');
        $activities =  $this->getRosterActivities($roster_html);
        return $activities;
    }

    /**
     * Loads a demo roster from a provided example file and saves it to the database
     *
     * @return array
     */
    public function seedDemoRoster()
    {
        $activities =  $this->loadDemoRoster();
        if( $activities  ){
            $this->saveActivities($activities);
        }
        return $activities;
    }

    /**
     * Loads a demo roster from a provided example file and saves it to the database
     *
     * @return array
     * @throws Exception
     */
    public function addActivitiesFromRoster( string $roster )
    {
        $activities =  $this->getRosterActivities($roster);
        if( $activities  ){
            $this->saveActivities($activities);
        }else{
            throw new Exception('No activities found in the roster');
        }
        return $activities;
    }

    /**
     * Saves the activities to the database
     *
     * @param Activity[] $activities
     * @return void
     */
    private function saveActivities( array $activities ){
        foreach ($activities as $activity){
            $activity->save();
            //insert:: requires all VALUES must have the same number of terms
        }
    }

}
