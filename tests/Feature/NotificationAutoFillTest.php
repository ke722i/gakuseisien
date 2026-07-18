<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationAutoFillTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_notification_form_auto_fills_from_authenticated_user(): void
    {
        $user = User::factory()->create([
            'student_number' => '234054',
            'class_number' => 'R4SA24',
            'student_name' => '高橋 陽子',
            'homeroom_teacher' => '片山先生',
        ]);

        $this->actingAs($user);

        $response = $this->get('/notification');

        $response->assertOk();
        $response->assertSee('name="student_number"', false);
        $response->assertSee('value="234054"', false);
        $response->assertSee('value="R4SA24"', false);
        $response->assertSee('value="高橋 陽子"', false);
        $response->assertSee('value="片山先生"', false);
    }
}
