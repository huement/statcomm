<?php
namespace Huement\StatComm\Http\Controllers;

use Illuminate\Http\Request;
use Statamic\Facades\Form;
use Statamic\Facades\FormSubmission;
use Statamic\Http\Controllers\CP\CpController as StatamicCpController;

class CpController extends StatamicCpController
{
  /**
   * Display the primary data stream logs matrix.
   */
  public function index()
  {
    // Query directly from database arrays and paginate 20 entries per page
    $comments = FormSubmission::query()
      ->where('form', 'blog_comments')
      ->orderBy('date', 'desc')
      ->paginate(20);

    return view('statcomm::cp.index', array(
      'title' => 'StatComm Transmission Core',
      'comments' => $comments,
    ));
  }

  /**
   * Fetch a specific comment submission and load the modification deck.
   */
  public function edit(string|int $id)
  {
    $form = Form::find('blog_comments');
    $comment = $form ? $form->submission($id) : null;

    if (!$comment) {
      return redirect()->route('statamic.cp.statcomm.index')->with('error', 'Target transmission path not found.');
    }

    return view('statcomm::cp.edit', array(
      'title' => 'Modify Comment Vector',
      'comment' => $comment,
    ));
  }

  /**
   * Validate and overwrite a comment's buffer payload.
   */
  public function update(Request $request, string|int $id)
  {
    $form = Form::find('blog_comments');
    $comment = $form ? $form->submission($id) : null;

    if (!$comment) {
      return redirect()->route('statamic.cp.statcomm.index')->with('error', 'Failed to commit updates: Target missing.');
    }

    // ⚡ Force the admin update array to match your core blueprint schema requirements
    $request->validate($form->blueprint()->rules());

    $comment->set('name', $request->name);
    $comment->set('comment', $request->comment);
    $comment->save();

    return redirect()->route('statamic.cp.statcomm.index')->with('success', 'Comment transmission updated successfully.');
  }

  public function approve(string|int $id)
  {
    $form = Form::find('blog_comments');
    $comment = $form ? $form->submission($id) : null;

    if ($comment) {
      $comment->set('approved', true);
      $comment->save();

      return redirect()->route('statamic.cp.statcomm.index')
        ->with('success', 'Transmission validated: Comment approved for public broadcasting.');
    }

    return redirect()->route('statamic.cp.statcomm.index')->with('error', 'Failed to isolate target packet.');
  }

  /**
   * Permanently purge a comment packet from the storage arrays.
   */
  public function destroy(string|int $id)
  {
    $form = Form::find('blog_comments');
    $comment = $form ? $form->submission($id) : null;

    if ($comment) {
      $comment->delete();
      return redirect()->route('statamic.cp.statcomm.index')->with('success', 'Comment packet successfully scrubbed from buffer.');
    }

    return redirect()->route('statamic.cp.statcomm.index')->with('error', 'Failed to locate target package for deletion.');
  }
}
