<?php
namespace Huement\StatComm\Livewire;

use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;
use Statamic\Facades\Form;
use Statamic\Facades\FormSubmission;

class StatComm extends Component
{
  use WithPagination;

  // Route parameters
  #[Locked]
  public string|int $articleId;

  // Pagination
  public int $perPage = 10;

  // Form inputs
  public string $name = '';
  public string $email = '';
  public string $comment = '';
  public string|int|null $parentId = null;

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
  protected function rules(): array
  {
    $form = Form::find('blog_comments');
    $rules = [];

    if ($form && $form->blueprint()) {
      // Loop through fields to extract your control panel validation flags
      foreach ($form->blueprint()->fields()->all() as $field) {
        $handle = $field->handle();

        // Map snake_case blueprint handles to match your camelCase Livewire properties
        if ($handle === 'article_id') {
          $handle = 'articleId';
        }
        if ($handle === 'parent_id') {
          $handle = 'parentId';
        }

        $fieldRules = $field->rules();
        if (!empty($fieldRules)) {
          $rules[$handle] = $fieldRules;
        }
      }
    }

    // ⚡ THE SAFEGUARD: If the blueprint reads empty (unpublished or missing config),
    // return rock-solid default arrays so Livewire never catches an empty ruleset.
    if (empty($rules)) {
      return [
        'name' => ['required', 'string', 'max:50'],
        'email' => ['required', 'email', 'max:100'],
        'comment' => ['required', 'string', 'max:2000'],
        'articleId' => ['required'],
        'parentId' => ['nullable', 'string'],
      ];
    }

    return $rules;
  }

  public function submit()
  {
    // SILENT HONEYPOT PURGE: If a bot populates this string field, terminate processing routes immediately
    if (!empty($this->honeypot_field)) {
      $this->reset(['name', 'email', 'comment', 'parentId', 'honeypot_field']);
      session()->flash('success', 'Your comment has been submitted.');
      return;
    }

    // ⚡ FIXED: Pass the rules array directly into the validation method
    $this->validate($this->rules());

    $form = Form::find('blog_comments');

    if (!$form) {
      session()->flash('error', 'The comment storage driver is not configured.');
      return;
    }

    // ⚡ READ THE CONFIG: If moderation is required, default to false
    $requireModeration = config('statcomm.require_moderation', true);

    $submission = $form->makeSubmission();
    $submission->data(array(
      'name' => strip_tags(trim($this->name)),
      'email' => trim(strtolower($this->email)),
      'comment' => strip_tags(trim($this->comment)),
      'article_id' => $this->articleId,
      'parent_id' => $this->parentId,
      'approved' => !$requireModeration, // ⚡ Sets status dynamically based on configuration
    ));

    $submission->save();

    $this->reset(array('name', 'email', 'comment', 'parentId', 'honeypot_field'));

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

    return view('statcomm::livewire.statcomm', array(
      'comments' => $comments,
    ));
  }
}
