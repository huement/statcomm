<?php

namespace Huement\StatComm\Tests;

use Huement\StatComm\Livewire\StatComm;
use Livewire\Livewire;
use Statamic\Facades\Form;
use Statamic\Facades\FormSubmission;

class StatCommComponentTest extends TestCase
{
    /**
     * Setup the test ecosystem variables.
     * Generates the virtual Statamic form definition inside the test runtime.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // ⚡ PROGRAMMATIC INJECTION: Create the mock form storage driver in memory
        Form::make('blog_comments')
            ->title('Blog Comments')
            ->save();
    }

    /** @test */
    public function component_mounts_cleanly_with_an_article_id(): void
    {
        Livewire::test(StatComm::class, ['articleId' => 'post-node-101'])
            ->assertStatus(200)
            ->assertViewHas('comments')
            ->assertSet('articleId', 'post-node-101');
    }

    /** @test */
    public function validation_firewall_rejects_empty_data_streams(): void
    {
        Livewire::test(StatComm::class, ['articleId' => 'post-node-101'])
            ->call('submit')
            ->assertHasErrors(['name', 'email', 'comment']);
    }

    /** @test */
    public function honeypot_field_activation_silently_halts_submission_intake(): void
    {
        Livewire::test(StatComm::class, ['articleId' => 'post-node-101'])
            ->set('name', 'Malicious Bot Agent')
            ->set('email', 'bot@spamnet.ru')
            ->set('comment', 'Executing systematic link inject algorithms...')
            ->set('honeypot_field', 'caught-you-sneaking') // ⚡ Triggers honeypot rule
            ->call('submit')
            ->assertHasErrors(['honeypot_field']);
            
        // Verify no data leaks into the underlying form submission datastore
        $this->assertEquals(0, FormSubmission::query()->where('form', 'blog_comments')->count());
    }

    /** @test */
    public function execution_protocol_saves_valid_comment_packet_to_buffer(): void
    {
        // 1. Assert the in-memory submission datastore is initially empty
        $this->assertEquals(0, FormSubmission::query()->where('form', 'blog_comments')->count());

        // 2. Initialize the Livewire data stream and execute submit pipeline
        Livewire::test(StatComm::class, ['articleId' => 'post-node-101'])
            ->set('name', 'Case Tester')
            ->set('email', 'tester@huement.com')
            ->set('comment', 'This is an official verification comment clearing runtime validation tracks.')
            ->call('submit')
            ->assertHasNoErrors()
            // Verifies fields flush clean after hitting a successful save sequence
            ->assertSet('name', '')
            ->assertSet('comment', '')
            ->assertSessionHas('success');

        // 3. Verify the core Statamic storage engine successfully captured the packet
        $this->assertEquals(1, FormSubmission::query()->where('form', 'blog_comments')->count());

        $savedSubmission = FormSubmission::query()->where('form', 'blog_comments')->first();
        $this->assertEquals('Case Tester', $savedSubmission->get('name'));
        $this->assertEquals('post-node-101', $savedSubmission->get('article_id'));
    }
}