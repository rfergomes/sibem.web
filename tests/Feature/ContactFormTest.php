<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful contact form submission.
     */
    public function test_contact_form_submission_is_successful()
    {
        $this->mock(NotificationService::class, function ($mock) {
            $mock->shouldReceive('createLandingContactNotification')->once();
        });

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'This is a test message.',
        ];

        $response = $this->post('/contact', $data);

        $response->assertStatus(200);
        $response->assertSeeText('OK');

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
        ]);
    }

    /**
     * Test contact form validation.
     */
    public function test_contact_form_validation_errors()
    {
        $response = $this->post('/contact', [
            'name' => '',
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
    }
}
