<?php

namespace Tests\Feature;

use App\Models\Visit;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class VisitTest extends TestCase
{
    /**
     * Test redirect from root to visits index.
     */
    public function test_root_redirects_to_visits_index(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('visits.index'));
    }

    /**
     * Test create visit page renders successfully.
     */
    public function test_visit_create_page_renders(): void
    {
        $response = $this->get(route('visits.create'));

        $response->assertStatus(200);
        $response->assertViewIs('visit.create');
        $response->assertSee('Check-In Lapangan');
        $response->assertSee('visitor_name');
        $response->assertSee('photoInput');
        $response->assertSee('notes');
        $response->assertSee('officersList');
    }

    /**
     * Test index page renders with filters and sorting options.
     */
    public function test_visit_index_page_renders_with_filters(): void
    {
        $response = $this->get(route('visits.index'));

        $response->assertStatus(200);
        $response->assertViewIs('visit.index');
        $response->assertSee('officerSelectFilter');
        $response->assertSee('sortSelectFilter');
        $response->assertSee('timeSelectFilter');
    }

    /**
     * Test store visit validation fails when fields are missing.
     */
    public function test_store_visit_fails_without_required_fields(): void
    {
        $response = $this->post(route('visits.store'), []);

        $response->assertSessionHasErrors([
            'visitor_name',
            'latitude',
            'longitude',
            'photo',
        ]);
    }

    /**
     * Test store visit validation fails for invalid coordinates.
     */
    public function test_store_visit_fails_for_invalid_coordinates(): void
    {
        $response = $this->post(route('visits.store'), [
            'visitor_name' => 'Budi',
            'latitude' => 150.0, // Invalid latitude (> 90)
            'longitude' => 200.0, // Invalid longitude (> 180)
            'photo' => UploadedFile::fake()->create('selfie.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertSessionHasErrors(['latitude', 'longitude']);
    }

    /**
     * Test ajax request with validation error returns json response.
     */
    public function test_ajax_store_visit_validation_error_returns_json(): void
    {
        $response = $this->postJson(route('visits.store'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'visitor_name',
            'latitude',
            'longitude',
            'photo',
        ]);
    }

    /**
     * Test store visit validation fails when notes exceeds 1000 characters.
     */
    public function test_store_visit_fails_if_notes_exceeds_max_length(): void
    {
        $response = $this->post(route('visits.store'), [
            'visitor_name' => 'Budi Santoso',
            'notes' => str_repeat('a', 1001),
            'latitude' => -7.25,
            'longitude' => 112.75,
            'photo' => UploadedFile::fake()->create('selfie.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertSessionHasErrors(['notes']);
    }
}
