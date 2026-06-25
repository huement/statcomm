<?php

namespace Huement\StatComm\Http\Controllers;

use Illuminate\Http\Request;
use Statamic\Facades\Form;
use Statamic\Http\Controllers\CP\CpController as StatamicCpController;

class CpController extends StatamicCpController
{
    /**
     * Display the primary data stream logs matrix.
     */
    public function index()
    {
        $form = Form::find('blog_comments');
        $comments = $form ? $form->submissions() : collect();
        $comments = $comments->sortByDesc(fn ($comment) => $comment->date());

        return view('statcomm::cp.index', [
            'title' => 'StatComm Transmission Core',
            'comments' => $comments,
        ]);
    }

    /**
     * Fetch a specific comment submission and load the modification deck.
     */
    public function edit($id)
    {
        $form = Form::find('blog_comments');
        $comment = $form ? $form->submission($id) : null;

        if (! $comment) {
            return redirect()->route('statcomm.index')->with('error', 'Target transmission path not found.');
        }

        return view('statcomm::cp.edit', [
            'title' => 'Modify Comment Vector',
            'comment' => $comment,
        ]);
    }

    /**
     * Validate and overwrite a comment's buffer payload.
     */
    public function update(Request $request, $id)
    {
        $form = Form::find('blog_comments');
        $comment = $form ? $form->submission($id) : null;

        if (! $comment) {
            return redirect()->route('statcomm.index')->with('error', 'Failed to commit updates: Target missing.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'comment' => 'required|string',
        ]);

        // Overwrite variables inside the storage engine payload
        $comment->set('name', $request->name);
        $comment->set('comment', $request->comment);
        $comment->save();

        return redirect()->route('statcomm.index')->with('success', 'Comment transmission updated successfully.');
    }

    /**
     * Permanently purge a comment packet from the storage arrays.
     */
    public function destroy($id)
    {
        $form = Form::find('blog_comments');
        $comment = $form ? $form->submission($id) : null;

        if ($comment) {
            $comment->delete();
            return redirect()->route('statcomm.index')->with('success', 'Comment packet successfully scrubbed from buffer.');
        }

        return redirect()->route('statcomm.index')->with('error', 'Failed to locate target package for deletion.');
    }
}