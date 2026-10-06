<?php

namespace App\Http\Controllers;

use App\Models\MembershipApplication;
use App\Models\MembershipApplicationForm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MembershipApplicationFormController extends Controller
{
    public function index(): View
    {
        return view('admin.membership-applications.index', [
            'forms' => MembershipApplicationForm::query()->latest()->withCount('applications')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.membership-applications.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'introduction' => ['nullable', 'string', 'max:3000'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.question' => ['required', 'string', 'max:1000'],
            'questions.*.type' => ['required', 'in:essay,choice'],
            'questions.*.options' => ['nullable', 'string', 'max:3000'],
            'questions.*.is_required' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($data, $request): void {
            $form = MembershipApplicationForm::create([
                'title' => $data['title'],
                'introduction' => $data['introduction'] ?? null,
                'public_token' => Str::random(40),
            ]);

            foreach ($data['questions'] as $index => $question) {
                $options = $question['type'] === 'choice'
                    ? collect(preg_split('/\r\n|\r|\n/', $question['options'] ?? ''))->map(fn (string $option): string => trim($option))->filter()->values()->all()
                    : null;

                if ($question['type'] === 'choice' && count($options) < 2) {
                    throw ValidationException::withMessages(['questions.'.$index.'.options' => 'Multiple-choice questions need at least two options.']);
                }

                $form->questions()->create([
                    'question' => $question['question'],
                    'type' => $question['type'],
                    'options' => $options,
                    'is_required' => $request->boolean("questions.$index.is_required"),
                    'sort_order' => $index,
                ]);
            }
        });

        return redirect()->route('admin.membership-applications.index')->with('success', 'Application link created.');
    }

    public function toggle(MembershipApplicationForm $membershipApplication): RedirectResponse
    {
        $membershipApplication->update(['is_open' => ! $membershipApplication->is_open]);

        return back()->with('success', $membershipApplication->is_open ? 'Applications are now open.' : 'Applications are now closed.');
    }

    public function applications(MembershipApplicationForm $membershipApplication): View
    {
        return view('admin.membership-applications.applications', [
            'form' => $membershipApplication,
            'applications' => $membershipApplication->applications()->latest()->paginate(20),
        ]);
    }

    public function export(): StreamedResponse
    {
        $approvedApplications = MembershipApplication::query()
            ->where('status', 'accepted')
            ->orderBy('applicant_name')
            ->get();

        $rows = collect($approvedApplications)->map(fn (MembershipApplication $application): array => [
            'name' => $application->applicant_name,
            'email' => $application->applicant_email,
        ]);

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'wb');
            fputcsv($handle, ['name', 'email']);

            foreach ($rows as $row) {
                fputcsv($handle, [$row['name'], $row['email']]);
            }

            fclose($handle);
        }, 'approved-member-applicants.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function showApplication(MembershipApplication $application): View
    {
        return view('admin.membership-applications.show', [
            'application' => $application->load(['form', 'answers.question', 'reviewer']),
        ]);
    }

    public function review(Request $request, MembershipApplication $application): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:accepted,rejected'],
            'decision_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $application->update($data + ['reviewed_by' => $request->user()->id, 'reviewed_at' => now()]);

        return back()->with('success', 'Application decision saved.');
    }
}
