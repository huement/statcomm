<?php

use Huement\StatComm\Tests\TestCase;

// Bind your custom AddonTestCase to all Feature and Unit tests in this folder
uses(TestCase::class)->in('Feature', 'Unit');
