<?php

namespace Tests\Feature;

use App\Enums\ActivityType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Activity;
use Tests\TestCase;

class ActivitiesTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {
        $posts = Activity::factory()->count(3)->create();

        $response = $this
            ->jsonApi()
            ->expects('activities')
            ->get('/api/activities?filter[dateFrom]=2023-01-12'); //To avoid default roster entries
        $response->assertFetchedMany($posts);
    }

    public function test_initial_roster(): void
    {
        $response = $this
            ->jsonApi()
            ->expects('activities')
            ->get('/api/activities');
        $response->assertStatus(200);
        assert(count($response->json()['data']) === 54); // 54 is the number of default roster entries
    }

    public function test_initial_roster_dates(): void
    {
        $response = $this
            ->jsonApi()
            ->expects('activities')
            ->get('/api/activities?filter[dateFrom]=2022-01-12&filter[dateTo]=2022-01-22');
        $response->assertStatus(200);
        assert(count($response->json()['data']) === 24); //TODO - maybe test against snapshot
    }

    public function test_bad_dates(): void
    {
        $response = $this
            ->jsonApi()
            ->expects('activities')
            ->get('/api/activities?filter[dateFrom]=bad-01-12&filter[dateTo]=2022-01-22');
        $response->assertStatus(400);

        $response = $this
            ->jsonApi()
            ->expects('activities')
            ->get('/api/activities?filter[dateFrom]=2021-01-12&filter[dateTo]=2022-22');
        $response->assertStatus(400);
    }

    public function test_initial_roster_types(): void
    {
        $response = $this
            ->jsonApi()
            ->expects('activities')
            ->get('/api/activities?filter[type]=' . ActivityType::FLIGHT->value);
        $response->assertStatus(200);
        assert(count($response->json()['data']) === 26); //TODO - maybe test against snapshot
    }

    public function test_bad_types(): void
    {
        $response = $this
            ->jsonApi()
            ->expects('activities')
            ->get('/api/activities?filter[type]=FLY');
        $response->assertStatus(400);
    }

    public function test_initial_roster_flights_from_location(): void
    {
        $response = $this
            ->jsonApi()
            ->expects('activities')
            ->get('/api/activities?filter[type]=FLT&filter[from]=KRP');
        $response->assertStatus(200);
        assert(count($response->json()['data']) === 12); //TODO - maybe test against snapshot
    }

    public function test_initial_roster_from_location(): void
    {
        $response = $this
            ->jsonApi()
            ->expects('activities')
            ->get('/api/activities?filter[from]=KRP');
        $response->assertStatus(200);
        assert(count($response->json()['data']) === 31);
    }

    public function test_flights_from_bad_location(): void
    {
        $response = $this
            ->jsonApi()
            ->expects('activities')
            ->get('/api/activities?filter[type]=FLT&filter[from]=CHINA');
        $response->assertStatus(400);
    }

    public function test_initial_roster_next_week_flights(): void
    {
        /**
         * @note Current date is mocked for this endpoint
         * @see Activity.php::$mockedToday
         */
        $response = $this
            ->jsonApi()
            ->expects('activities')
            ->get('/api/activities?filter[week]=next&filter[type]=FLT');
        $response->assertStatus(200);
        assert(count($response->json()['data']) === 18);
    }

    public function test_bad_week(): void
    {
        /**
         * @note Current date is mocked for this endpoint
         * @see Activity.php::$mockedToday
         */
        $response = $this
            ->jsonApi()
            ->expects('activities')
            ->get('/api/activities?filter[week]=last');
        $response->assertStatus(400);
    }

}
