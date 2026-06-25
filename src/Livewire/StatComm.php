<?php

namespace Huement\StatComm\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Statamic\Facades\Form;
use Statamic\Facades\FormSubmission;

class StatComm extends Component
{
    use WithPagination;

    // Route parameters
    public $articleId;

    // Pagination
    public $perPage = 10;

    // Form inputs
    public $name = '';
    public $email = '';
    public $comment = '';
    public $parentId = null;

    // Honeypot field for invisible spam protection
    public $honeypot_field = '';

    /**
     * Mount the component with the current article's ID.
     */
    public function mount($articleId, $perPage = null)
    {
        $this->articleId = $articleId;

        if ($perPage) {
            $this->perPage = $perPage;
        }
    }

    /**
     * Natively pull validation rules directly from the Statamic CP Form Blueprint!
     */
    protected function rules()
    {
        $form = Form::find('blog_comments');

        // Fallback safety baseline rules if the form doesn't exist yet
        if (!$form) {
            return [
                'name' => 'required|string|max:50',
                'email' => 'required|email|max:100',
                'comment' => 'required|string|max:2000',
            ];
        }

        $rules = [];

        // Loop through fields to extract your control panel validation flags
        foreach ($form->blueprint()->fields()->all() as $field) {
            $fieldRules = $field->config()['validate'] ?? [];

            if (is_string($fieldRules)) {
                $fieldRules = explode('|', $fieldRules);
            }

            // Livewire requires non-validated variables to have a fallback bound tag
            $rules[$field->handle()] = empty($fieldRules) ? ['nullable'] : $fieldRules;
        }

        // Keep your specialized custom honeypot constraint locked down
        $rules['honeypot_field'] = ['nullable', 'max:0'];
        $rules['parentId'] = ['nullable', 'string'];

        return $rules;
    }

    public function submit()
    {
        $this->validate();

        $form = Form::find('blog_comments');
        if (!$form) {
            session()->flash('error', 'The comment storage driver is not configured.');
            return;
        }

        // ⚡ READ THE CONFIG: If moderation is required, default to false
        $requireModeration = config('statcomm.require_moderation', true);

        $submission = $form->makeSubmission();
        $submission->data([
            'name' => strip_tags(trim($this->name)),
            'email' => trim(strtolower($this->email)),
            'comment' => strip_tags(trim($this->comment)),
            'article_id' => $this->articleId,
            'parent_id' => $this->parentId,
            'approved' => !$requireModeration, // ⚡ Sets status dynamically based on configuration
        ]);

        $submission->save();

        $this->reset(['name', 'email', 'comment', 'parentId', 'honeypot_field']);

        // Customize message depending on moderation rule states
        $message = $requireModeration
            ? 'Your comment has been submitted and is holding for administrative moderation approval.'
            : 'Your comment has been posted successfully!';

        session()->flash('success', $message);
        $this->gotoPage(1);
    }

    public function render()
    {
        $query = FormSubmission::query()
            ->where('form', 'blog_comments')
            ->where('article_id', $this->articleId);

        // ⚡ FILTER THE BUFFER: Hide unapproved entries if moderation is active
        if (config('statcomm.require_moderation', true)) {
            $query->where('approved', true);
        }

        $comments = $query->orderBy('date', 'desc')->paginate($this->perPage);

        return view('statcomm::livewire.statcomm', [
            'comments' => $comments,
        ]);
    }
}
