---
description: Run Sellia Flow Builder code review
---

Review recent code changes in two steps:

## Step 1: Sellia Review
Use the sellia-code-reviewer agent to analyze files changed since last commit. Focus on:
- Livewire anti-patterns
- Unnecessary complexity for a prototype
- Flow builder specific concerns
- Data structure consistency

## Step 2: Laravel Pint
After the review finishes, run:
```bash
./vendor/bin/pint --dirty
```

Show results from both steps.
