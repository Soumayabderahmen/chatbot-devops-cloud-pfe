<?php
namespace Tests\Unit\Admin;

use App\Models\Faq;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFaqControllerTest extends TestCase
{
    use RefreshDatabase;

  public function test_index_displays_faqs()
{
    $faq = Faq::factory()->create();

    $admin = \App\Models\User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('admin.faqs.index'));
   
    $response->assertStatus(200);
    $response->assertViewHas('faqs');

    $faqs = $response->viewData('faqs');
   

    $this->assertCount(1, $faqs);
    $this->assertEquals($faq->question, $faqs->first()->question); // Optionnel, pour plus de précision
}

}
