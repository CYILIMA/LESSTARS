<?php

namespace App\Http\Controllers;

use App\Models\MembershipApplicationForm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MembershipApplicationController extends Controller
{
    public function create(MembershipApplicationForm $form): View
    {
        abort_unless($form->is_open, 404);

        return view('membership-application', ['form' => $form->load('questions')]);
    }

    public function store(Request $request, MembershipApplicationForm $form): RedirectResponse
    {
        abort_unless($form->is_open, 404);

        $data = $request->validate([
            'applicant_name' => ['required', 'string', 'max:255'],
            'applicant_email' => ['required', 'email', 'max:255'],
            'answers' => ['array'],
        ]);

        if ($form->applications()->where('applicant_email', $data['applicant_email'])->exists()) {
            throw ValidationException::withMessages(['applicant_email' => 'An application has already been submitted with this email address.']);
        }

        DB::transaction(function () use ($form, $data): void {
            $application = $form->applications()->create([
                'applicant_name' => $data['applicant_name'],
                'applicant_email' => $data['applicant_email'],
            ]);

            foreach ($form->questions as $question) {
                $answer = trim((string) ($data['answers'][$question->id] ?? ''));

                if ($question->is_required && blank($answer)) {
                    throw ValidationException::withMessages(["answers.{$question->id}" => 'This question is required.']);
                }

                if ($question->type === 'choice' && filled($answer) && ! in_array($answer, $question->options ?? [], true)) {
                    throw ValidationException::withMessages(["answers.{$question->id}" => 'Choose one of the available options.']);
                }

                $application->answers()->create([
                    'membership_application_question_id' => $question->id,
                    'answer' => $answer ?: null,
                ]);
            }
        });

        return redirect()->route('membership-applications.thank-you')->with('application_submitted', true);
    }

    public function thankYou(): View
    {
        abort_unless(session('application_submitted'), 404);

        return view('membership-application-thank-you');
    }
}
