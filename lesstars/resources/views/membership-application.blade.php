<x-site title="{{ $form->title }} — Les Stars Du Theatre">
    <section class="application-page">
        <div class="application-intro"><p class="eyebrow">MEMBER STAR APPLICATION</p><h1>{{ $form->title }}</h1>@if($form->introduction)<p>{{ $form->introduction }}</p>@endif</div>
        <form class="application-public-form" method="POST" action="{{ route('membership-applications.store', $form) }}">
            @csrf
            <label>Full name<input name="applicant_name" value="{{ old('applicant_name') }}" autocomplete="name" required autofocus></label>
            <label>Email address<input type="email" name="applicant_email" value="{{ old('applicant_email') }}" autocomplete="email" required></label>
            @foreach($form->questions as $question)
                <fieldset><legend>{{ $question->question }} @if($question->is_required)<em>Required</em>@endif</legend>
                    @if($question->type === 'choice')
                        <div class="application-choices">@foreach($question->options ?? [] as $option)<label><input type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}" @checked(old("answers.{$question->id}") === $option) @if($question->is_required) required @endif> {{ $option }}</label>@endforeach</div>
                    @else
                        <textarea name="answers[{{ $question->id }}]" @if($question->is_required) required @endif placeholder="Write your answer here...">{{ old("answers.{$question->id}") }}</textarea>
                    @endif
                    @error("answers.{$question->id}")<p class="form-error">{{ $message }}</p>@enderror
                </fieldset>
            @endforeach
            @if($errors->has('applicant_name') || $errors->has('applicant_email'))<p class="form-error">{{ $errors->first('applicant_name') ?: $errors->first('applicant_email') }}</p>@endif
            <button class="button button-blue" type="submit">Submit application <span>→</span></button>
        </form>
    </section>
</x-site>
