<x-admin.layout title="Create Member Application">
    <div class="admin-heading"><div><p class="eyebrow blue">RECRUITMENT</p><h1>Create application</h1></div></div>
    <form class="admin-form application-builder" method="POST" action="{{ route('admin.membership-applications.store') }}">
        @csrf
        <label>Application title<input name="title" value="{{ old('title', 'Apply to become a Member Star') }}" required></label>
        <label>Introduction<textarea name="introduction" placeholder="Tell applicants about the opportunity and what to expect.">{{ old('introduction') }}</textarea></label>
        <div class="application-builder-heading"><div><b>Application questions</b><p>Use essay questions for written answers, or multiple choice for a single selection.</p></div><button type="button" class="button button-blue" data-add-question>Add question <span>+</span></button></div>
        <div data-question-list></div>
        @if($errors->any())<p class="form-error">{{ $errors->first() }}</p>@endif
        <button class="button button-blue" type="submit">Create public application link <span>→</span></button>
    </form>
    <template data-question-template><fieldset class="application-question"><button type="button" class="remove-question" aria-label="Remove question">×</button><label>Question<input data-field="question" required></label><label>Answer type<select data-field="type"><option value="essay">Essay answer</option><option value="choice">Multiple choice</option></select></label><label data-options-field hidden>Options — one per line<textarea data-field="options" placeholder="First option&#10;Second option"></textarea></label><label class="remember"><input type="checkbox" data-field="is_required" value="1" checked> Required question</label></fieldset></template>
</x-admin.layout>
