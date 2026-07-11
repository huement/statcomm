<?php

use Statamic\Facades\Form;
use Statamic\Facades\Role;
use Statamic\Facades\User;

beforeEach(function () {
  // Ensure the 'blog_comments' form template exists in the test container environment
  Form::make('blog_comments')->handle('blog_comments')->save();
});

/*
|--------------------------------------------------------------------------
| Guest / Unauthenticated Barriers
|--------------------------------------------------------------------------
*/

it('intercepts unauthenticated guests and forces a login redirect', function () {
  $this->get(cp_route('statcomm.index'))
    ->assertRedirect();
});

/*
|--------------------------------------------------------------------------
| Unauthorized Guard Checks (302 Redirects to CP Home)
|--------------------------------------------------------------------------
*/

it('denies dashboard listing access to users missing the view privilege', function () {
  // Give them general CP entry access, but withhold the comment privileges
  Role::make('base_cp_user')->title('Base User')->addPermission('access cp')->save();

  $user = User::make()->email('unauthorized@huement.com')->assignRole('base_cp_user')->save();

  // ⚡ FIX: Assert that Statamic successfully redirects them away from the route
  $this->actingAs($user)
    ->get(cp_route('statcomm.index'))
    ->assertRedirect();
});

it('blocks comment update permissions for unauthorized users', function () {
  // Give them general CP entry access, but withhold the comment permissions
  Role::make('base_cp_user')->title('Base User')->addPermission('access cp')->save();

  $user = User::make()->email('unauthorized@huement.com')->assignRole('base_cp_user')->save();

  // ⚡ FIX: Assert that Statamic successfully redirects them away from the route
  $this->actingAs($user)
    ->post(cp_route('statcomm.approve', 'transmission-id-999'))
    ->assertRedirect();
});

/*
|--------------------------------------------------------------------------
| Authorized Passes (200 & 302 Successes)
|--------------------------------------------------------------------------
*/

it('bypasses restrictions entirely for designated super users', function () {
  $superUser = User::make()
    ->email('boss@huement.com')
    ->makeSuper()
    ->save();

  $this->actingAs($superUser)
    ->get(cp_route('statcomm.index'))
    ->assertStatus(200)
    ->assertViewIs('statcomm::cp.index');
});

it('grants access to roles explicitly bundled with the view comments permission', function () {
  // ⚡ FIX: Bundle both access cp AND view comments together
  Role::make('moderator')
    ->title('Comment Moderator')
    ->addPermission('access cp')
    ->addPermission('view comments')
    ->save();

  // 2. Assign role to standard user
  $staffUser = User::make()
    ->email('moderator@huement.com')
    ->assignRole('moderator')
    ->save();

  // 3. Request should bypass gate cleanly
  $this->actingAs($staffUser)
    ->get(cp_route('statcomm.index'))
    ->assertStatus(200);
});

it('authorizes execution of administrative functions for authorized users', function () {
  // ⚡ FIX: Bundle both access cp AND edit comments together
  Role::make('admin')
    ->title('Admin')
    ->addPermission('access cp')
    ->addPermission('edit comments')
    ->save();

  $adminUser = User::make()
    ->email('admin@huement.com')
    ->assignRole('admin')
    ->save();

  // 2. Mock a test submission payload directly into active cache memory
  $form = Form::find('blog_comments');
  $submission = $form->makeSubmission();
  $submission->id('active-packet-001');
  $submission->data(['name' => 'John Doe', 'comment' => 'Secure payload text']);
  $submission->save();

  // 3. Confirm target execution and tracking validation variables match
  $this->actingAs($adminUser)
    ->post(cp_route('statcomm.approve', 'active-packet-001'))
    ->assertRedirect(cp_route('statcomm.index'))
    ->assertSessionHas('success');
});
