# DESIGN DOCUMENT
## STATAMIC COMMENTS SYSTEM 

----

## 1. ARCHITECTURAL OVERVIEW
The **StatComm** system is a high-performance, standalone, distributed Statamic addon engineered to handle real-time User-Generated Content (comments). Instead of relying on rigid, third-party database tables or monolithic file structures, the system treats comments as entries managed under a standardized Statamic Form blueprint (blog_comments).
By building directly over Statamic's native Form Submission core, the architecture automatically inherits whichever storage engine is set in your application's config/statamic/eloquent-driver.php. If the parent application operates on flat-files, comments stream cleanly into structured YAML; if it relies on an database driver, comments write seamlessly to SQL tables without requiring local database migration scripts.

```
[Frontend Client] ──(Livewire 3 Engine)──> [StatComm Package Core]
                                                   │     
                     ┌─────────────────────────────┴─────────────────────────────┐
                     ▼                                                           ▼
       [Statamic Forms Driver]                                      [Control Panel Interceptor]
  (YAML Flat-Files OR Eloquent Tables)                          (Volumetrics, Density, Trace Scrub)
```

### Core Architecture Enhancements
* **Decoupled Package Universe:** Migrated from a local hardcoded app directory into an isolated, PSR-4 compliant (`Huement\StatComm`) distribution-ready composer package installable via Packagist.
* **Dynamic Validation Intake:** Eliminates static code validation rules. The core engine queries the active Statamic Form blueprint config at runtime, extracting rule strings and requirement parameters automatically.
* **Dual-View Telemetry Matrix:** Consists of an inline comment workflow stream alongside an isolated dashboard widget module designed to inject cross-referenced source entries onto client landing pages.
* **Dedicated Control Panel Monitor:** Establishes an absolute data auditing interface inside the Statamic sidebar menu. It processes advanced analytics (volumetrics, character density, timelines) and executes destructive database trace-scrubbing.

## 2. PACKAGE CONFIGURATION & FILE MATRIX
The package operates autonomously under the Huement\StatComm namespace. The file ecosystem is organized within a clean separation of concerns pipeline:
| **Target File Path** | **Object Class / Type** | **Functional Execution Node** |
|---|---|---|
| src/ServiceProvider.php | Addon Service Provider | Registers views, config, asset paths, Livewire tags, and secure CP routers. |
| src/Livewire/StatComm.php | Livewire Component | Controls input validation loops, programmatic submission, and frontend loops. |
| src/Livewire/StatCommWidget.php | Livewire Component | Manages standalone telemetry dashboard widgets cross-referencing Entry slugs. |
| src/Http/Controllers/CpController.php | Statamic CP Controller | Powers analytics matrices, data payload updates, and absolute trace purges. |
| resources/views/livewire/statcomm.blade.php | Blade Layout Template | Cyberpunk-themed visual input deck and comment timeline history stream. |
| resources/views/livewire/statcomm-widget.blade.php | Blade Layout Template | Pitch-black dashboard telemetric overview monitoring active comment streams. |
| resources/views/cp/index.blade.php | Blade CP Template | Volumetric logs display complete with selection flags and density status metrics. |
| resources/views/cp/edit.blade.php | Blade CP Template | Modification canvas deck to edit, re-verify, or overwrite payload buffers. |

## 3. CORE SUB-SYSTEM ENGINEERING
### A. The Storage Definition Blueprint

File Target: `resources/forms/blog_comments.yaml`

The blueprint maps data arrays to the underlying storage files or Eloquent structures. It includes built-in honeypot variables and structural identification nodes. It also allows you to FORCE comments to be approved before they appear on the page. Pretty cool right? 

Here is the file that does most of the heavy lifting in terms of configuration. 

```yaml
title: 'Blog Comments'
honeypot: honeypot_field
blueprint:
    sections:
        main:
            fields:
                - handle: name
                  field:
                      type: text
                      display: 'Name'
                      validate: 'required|min:2|max:50'
                - handle: email
                  field:
                      type: text
                      input_type: email
                      display: 'Email Address'
                      validate: 'required|email'
                - handle: comment
                  field:
                      type: textarea
                      display: 'Comment'
                      validate: 'required|min:10|max:2000'
                - handle: article_id
                  field:
                      type: text
                      display: 'Article ID'
                      visibility: hidden
                      validate: 'required'
                - handle: parent_id
                  field:
                      type: text
                      display: 'Parent Comment ID'
                      visibility: hidden
                - handle: approved
                  field:
                      type: toggle
                      display: 'Approved Status'
                      default: false
                      visibility: visible

```

### B. Polymorphic Blueprint Validation Blueprint
File Target: `src/Livewire/StatComm.php`
Instead of hardcoding rules, the system inspects the field configurations within the Statamic Control Panel file maps. Unvalidated variables are assigned a fallback nullable flag array to satisfy Livewire's binding requirements:

```php
protected function rules()
{
    $form = Form::find('blog_comments');
    if (!$form) {
        return [
            'name' => 'required|string|max:50',
            'email' => 'required|email|max:100',
            'comment' => 'required|string|max:2000',
        ];
    }

    $rules = [];
    foreach ($form->blueprint()->fields()->all() as $field) {
        $fieldRules = $field->config()['validate'] ?? [];
        if (is_string($fieldRules)) {
            $fieldRules = explode('|', $fieldRules);
        }
        $rules[$field->handle()] = empty($fieldRules) ? ['nullable'] : $fieldRules;
    }

    $rules['honeypot_field'] = ['nullable', 'max:0'];
    $rules['parentId'] = ['nullable', 'string'];

    return $rules;
}
```

### C. Live Telemetry Widget Resolution
File Target: `src/Livewire/StatCommWidget.php`
The dashboard widget reads recent records and transforms them into standard arrays. It securely resolves relational source entry data to map accurate navigation links and titles back to the frontend dashboard:

```php
$recentComments = $submissions
    ->sortByDesc(fn ($submission) => $submission->date())
    ->take($this->limit)
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
```

## 4. REFACTORED OPERATIONAL WORKFLOW
```
[Article Detail Page View]
   │
   ├── 1. Read entry UUID parameter 
   ├── 2. Query FormSubmission repository via database pagination chunks (Speed optimized ⚡)
   └── 3. Clean string payloads through ContentRenderer utility structures
         │
         ▼
[User Submits Input Data]
   │
   ├── 1. Run honeypot zero-length tracking validations
   ├── 2. Extract input validation parameters directly from form YAML configuration maps
   ├── 3. Execute Form::makeSubmission() data instantiation mapping
   └── 4. Fire Livewire pagination fallback reset, pushing view to Page 1 instantly
```

## 5. RECOGNIZED COMPILER PROTECTIONS (BUG HISTORY FIXES)
* **The Stale View Cache Cache Trap:** Patched template errors (unexpected end of file) caused by invisible non-breaking space characters (\u00a0) embedded within copied markdown code blocks. The package compiler codebase is completely sanitized, and developers are instructed to execute php artisan view:clear to drop old token structures.
* **The Directive Collision Shield:** Applied double @@ literal character escaping on strict CSS definitions (@@import) and structured graph scripts ("@@context", "@@type") inside Blade files. This blocks Laravel's regex compiler from misinterpreting text formatting as unmapped system macros.
* **The Initials Generation Multi-Word Loop:** Integrated fallback data_get and explicit loop limits to process user names natively into clean initials badges without breaking when encountering empty data arrays or nested collection properties.

---
## 6. FUTURE ENGINEERING ROADMAP 
#### AKA THE TODO LIST

### Phase 1: Security & Spam Defenses
* [ ] **Turnstile Integration:** Implement Cloudflare Turnstile token verification within StatComm::submit as an alternative to the native honeypot.
* [ ] **Akismet API Interceptor:** Run the comment body payload through Akismet's spam detection API before invoking $submission->save().
* [ ] **IP Rate-Limiting Throttler:** Use Laravel's RateLimiter facade to restrict comment transmissions to a maximum of 3 submissions per IP address per hour.

## Phase 2: Interface & UI Upgrades
* [ ] **Nested Multi-Thread Replies Visualizer:** Update the blade view and loop models to support rendering visual reply chains nested up to 3 levels deep.
* [ ] **Livewire Real-time Feed Appending:** Replace the hardcoded gotoPage(1) command with Livewire 3's #[On] event polling system to instantly prepend new items onto the list without modifying pagination states.
* [ ] **Markdown Engine Core Integration:** Allow users to write safe GitHub-Flavored Markdown inside comments, parsing it through the ContentRenderer via an approved HTML sanitizer like HTMLPurifier.

## Phase 3: Administrative Control Panel Tools
* [ ] **Bulk Moderation Actions Array:** Add JavaScript multi-select checkboxes to the CP index table view allowing admins to approve, spam, or delete multiple comments simultaneously.
* [ ] **Autonomous Slack/Discord Notification Webhooks:** Add environment configuration flags (STATCOMM_WEBHOOK_URL) to broadcast incoming messages to team chat rooms instantly.
* [ ] **AI-Driven Sentiment Analysis Tagging:** Run comment payloads through a local text analysis engine during the ingestion cycle, flagging aggressive or hostile inputs for manual administrative review.

----

### SUGGESTIONS | COMMENTS | REQUESTS

If you have any suggestions, or things you want to see or things you’re seeing and hating, then use the Github repository, or use the contact form on the [https://huement.com](https://huement.com) website. 

#Huement