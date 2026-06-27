<?php

use Huement\StatComm\Livewire\StatComm;
use Huement\StatComm\Tests\TestCase;
use Livewire\Livewire;
use Statamic\Facades\Form;

uses(TestCase::class);

// Setup a clean helper state before each test if needed
beforeEach(function () {
    // Ensure your Statamic 'blog_comments' form exists in the test container
    // Form::make('blog_comments')->save();
});

it('fails validation when mandatory comment fields are left blank', function () {
    Livewire::test(StatComm::class, ['articleId' => 'test-post-1'])
        ->set('name', '')
        ->set('email', '')
        ->set('comment', '')
        ->call('submit')
        ->assertHasErrors(['name', 'email', 'comment']);
});

it('allows successful comment submissions when validation passes', function () {
    Livewire::test(StatComm::class, ['articleId' => 'test-post-1'])
        ->set('name', 'Skellie Dev')
        ->set('email', 'skull@huement.com')
        ->set('comment', 'This cyberpunk polygon wave web component rules!')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertStatus(200);
});

it('silently ignores submissions when the honeypot field is trapped', function () {
    Livewire::test(StatComm::class, ['articleId' => 'test-post-1'])
        ->set('name', 'Spam Bot')
        ->set('email', 'bot@spam.com')
        ->set('comment', 'Buy crypto now links here')
        ->set('honeypot_field', 'gotcha!') // ⚡ Fill the trap
        ->call('submit')
        ->assertHasNoErrors() // Fails silently
        ->assertSet('name', ''); // Resets cleanly
});
