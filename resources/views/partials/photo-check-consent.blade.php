{{-- Step 8: photo check line for the data consent box (landing and resident Submit Concern). --}}
@if (config('photo_check.driver') === 'ai')
    Your photo is checked automatically, including by an AI service (Anthropic's Claude), for signs it is AI-generated, reused, unrelated or unsafe. A flagged photo is only marked for an official to review; it never rejects your report.
@else
    Your photo is checked automatically for signs it is AI-generated, reused or edited. A flagged photo is only marked for an official to review; it never rejects your report.
@endif
