# StatComm Development Guide

First off, thank you for taking the time to contribute! It’s people like you who keep open-source tools robust, fast, and reliable.

We enthusiastically welcome bug fixes, feature requests, documentation improvements, and optimizations. This document outlines the local development setup, workflow, and expectations for submitting pull requests.

---

## 🛠️ Local Development Setup

The easiest way to develop on StatComm is by linking it locally to an existing Statamic v5 project using a Composer path repository. This allows you to test your changes in a real application sandbox in real-time. For reference, the plugins Github page [https://github.com/huement/statcomm](https://github.com/huement/statcomm),

### 1. Structure Your Workspace

Place the repository inside your main Statamic project's `addons` folder:

```text
your-statamic-project/
└── addons/
    └── huement/
        └── statcomm/  <-- Clone this repository here
```

### 2. Configure Your Local App's composer.json

To tell your root application to pull the addon from your local disk instead of Packagist, add a path repository wrapper above your require block:

```json
"repositories": [
    {
        "type": "path",
        "url": "addons/statcomm",
        "options": {
            "symlink": true
        }
    }
],
"require": {
    "huement/statcomm": "*@dev"
}
```

### 3. Rebuild Your Environment Mappings

If you are using **Laravel Sail**, refresh your container's autoloader:
Bash

```
./vendor/bin/sail composer update huement/statcomm --prefer-source
```

## 📐 Coding Standards & Expectations

To keep the codebase predictable, secure, and clean, please adhere to the following paradigms when writing code:

### 1. Strict Typing everywhere

We prioritize strong typing in modern PHP. All new classes, methods, and properties **must** declare visibility modifiers, strict type-hints, and return types.
PHP

```php
// 👍 Good: Properties and methods are strictly typed

public string $articleId;

protected function rules(): array
{
	return [];
}
```

### 2. Livewire Property Binding Conventions

- **Livewire Properties**: Always use camelCase for public properties inside components (e.g., $articleId, $parentId).
- **Statamic Blueprints**: Use standard snake_case handles in your YAML fields.
- **Mapping**: Ensure handles are mapped cleanly in the validation state so that Livewire's dynamic rule-binder never fails silently.

### 3. Security First

- Never expose mutable record markers or global scope keys to the frontend. Always enforce the #[Locked] attribute on identifying parameter hooks like $articleId.
- Always treat incoming user-generated content as hostile. Ensure inputs are sanitized via native string formatting methods before being written to disk buffers.

---

## TESTS

This plugin has a _somewhat_ robust testing suit. Its more than most of plugins get, and it will continue to expand as the plugin grows.

```bash
sail@ff855c114616:/var/www/html/addons/statcomm$ ./vendor/bin/pest

   PASS  Tests\Feature\CpControllerTest
  ✓ it intercepts unauthenticated guests and forces a login redirect                                                                         0.70s
  ✓ it denies dashboard listing access to users missing the view privilege                                                                   0.44s
  ✓ it blocks comment update permissions for unauthorized users                                                                              0.39s
  ✓ it bypasses restrictions entirely for designated super users                                                                             0.50s
  ✓ it grants access to roles explicitly bundled with the view comments permission                                                           0.51s
  ✓ it authorizes execution of administrative functions for authorized users                                                                 0.41s

   PASS  Tests\Feature\StatCommComponentTest
  ✓ it fails validation when mandatory comment fields are left blank                                                                         0.11s
  ✓ it allows successful comment submissions when validation passes                                                                          0.09s
  ✓ it silently ignores submissions when the honeypot field is trapped                                                                       0.09s

  Tests:    9 passed (20 assertions)
  Duration: 3.33s

sail@ff855c114616:/var/www/html/addons/statcomm$
```

StatComm features a robust integration testing suite built on **Pest PHP** and **Orchestra Testbench**. The tests are structured as isolated package feature tests, meaning they boot up a miniature, secure virtual Laravel/Statamic container environment to run component assertions without polluting your physical local databases or application state.

### What We Are Using

- **Pest PHP**: A modern, elegant, and functional testing framework wrapper around PHPUnit.
- **Orchestra Testbench**: The industry-standard tool for Laravel package development that generates an isolated sandbox container runtime to load package service providers.
- **Livewire Component Testing**: Natively handles component lifecycle triggers, state mutation (`->set()`), validation checks, and action execution (`->call()`).

### What We Are Testing

The automated test suite runs comprehensive checks against the core communication engine (`StatComm` Livewire component), verifying several critical operations:

1. **Strict Blueprint Validation**: Asserts that blank, incomplete, or corrupted submission payloads are blocked at the perimeter and throw appropriate validation errors matching your core Statamic configuration rules (`name`, `email`, `comment`).
2. **Successful Post Transitions**: Asserts that clean data arrays bypass validation rules, successfully dispatch saving events to the Statamic form submission driver, clear active form parameters, and broadcast flashing success notices to the user interface layer.
3. **Silent Honeypot Purging**: Asserts that automatic spam script submissions populating hidden fields (`honeypot_field`) are caught instantly, reset completely, and terminated via an early exit pipeline before hitting validation rules or writing malicious bytes to storage.

### Testing Prerequisites

Because this is a local development addon linked using a Composer `path` repository, your parent application's test runner needs to understand where your addon namespaces live. Ensure your main project's `composer.json` handles the target mappings under its development blocks:

```json
"autoload-dev": {
    "psr-4": {
        "Tests\\": "tests/",
        "Huement\\StatComm\\Tests\\": "addons/huement/statcomm/tests/"
    }
},
"require-dev": {
    "orchestra/testbench": "^8.0"
}
```

_Note: After updating your project's root composer.json, remember to re-index mappings inside your container environment:_

```bash
./vendor/bin/sail composer dump-autoload
```

### Running the Test

You can run the full addon test block seamlessly straight from the root directory of your primary Statamic installation via Laravel Sail:

```bash
# Run the complete component test suite using Pest via Sail
./vendor/bin/sail pest addons/huement/statcomm/tests/Feature/StatCommComponentTest.php

# Alternatively, using the Laravel Artisan test runner wrapper
./vendor/bin/sail artisan test addons/huement/statcomm/tests/Feature/StatCommComponentTest.php
```

#### Isolating Specific Assertions

If you are modifying code blocks and only want to execute a specific test case without firing the entire file stack, append the --filter flag:

```bash
./vendor/bin/sail pest addons/huement/statcomm/tests/Feature/StatCommComponentTest.php --filter="silently ignores submissions"
```

---

### 🧪 Testing Before You Push

We maintain a strict rule: **No pull requests will be merged without accompanying tests or passing suites.** StatComm utilizes a high-speed integration test framework combining **Pest PHP** and **Orchestra Testbench**. Ensure your changes don't break existing tracks by executing the test command through your container root:
Bash

### Run the core addon suite

```bash
# Running this from the sail shell. If you dont use sail then adjust accordingly.
$ > sail shell

# However you get there, you just run pest from the addon root directory.
sail@ff855c114616:/var/www/html/addons/statcomm$ ./vendor/bin/pest
```

If you are writing a new feature or fixing a bug, please write an accompanying test statement inside StatCommComponentTest.php using the clean functional Pest syntax:
PHP

```php
it('describes your new behavior or bug fix assertion', function () {
	// Assert status, properties, or validation hooks here...
});
```

## 🚀 Submitting a Pull Request

Ready to ship? Follow this workflow to ensure a smooth review lifecycle:
**1** **Fork the repo** and create a feature branch off of main using a descriptive name:
_ git checkout -b feature/your-awesome-feature
_ git checkout -b fix/issue-with-validation
**2** **Commit your changes** with clear, descriptive commit messages.
**3** **Run the tests** to guarantee the suite is entirely green.
**4** **Push to your fork** and submit a Pull Request against our main branch.

### PR Checklist

Before opening your pull request, double-check that you have completed the following:

- [ ] Code adheres to strict typing guidelines.
- [ ] Local Pest tests pass completely (0.00s failures).
- [ ] New features or fixes include updated regression testing steps.
- [ ] The README.md or configuration guides have been updated if any public-facing API changed.

---

## REPOSITORY SUBMISSION DETAILS

**Once submitted, our core team will review your code as quickly as possible. We look forward to collaborating with you!**

If you have less technical questions, or just want to say high, please drop by the [https://huement.com](https://huement.com) website, where you can see the comment plugin in action!
