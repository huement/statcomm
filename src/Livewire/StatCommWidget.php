<?php

namespace Huement\StatComm\Livewire;

use Livewire\Component;
use Statamic\Facades\Entry;
use Statamic\Facades\Form;

class StatCommWidget extends Component
{
    // ⚡ CUSTOMIZATION VECTORS
    public $heading = null;   // Custom string text heading

    public $limit = 5;        // Dynamic data count cutoff

    public $showDate = true;  // Toggle layout date metrics

    public function render()
    {
        $form = Form::find('blog_comments');
        $submissions = $form ? $form->submissions() : collect();

        $recentComments = $submissions
            ->sortByDesc(fn ($submission) => $submission->date())
            ->take($this->limit) // Leverages your dynamic limit property
            ->map(function ($submission) {
                $articleId = $submission->get('article_id');
                $entry = $articleId ? Entry::find($articleId) : null;

                return [
                    'name' => $submission->get('name'),
                    'comment' => $submission->get('comment'),
                    'date' => $submission->date(),
                    'post_url' => $entry ? $entry->url() : '#',
                    'post_title' => $entry ? $entry->get('title') : 'Unknown Sector Link',
                ];
            });

        return view('statcomm::livewire.statcomm-widget', [
            'recentComments' => $recentComments,
        ]);
    }
}
