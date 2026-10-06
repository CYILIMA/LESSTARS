<?php

namespace App\Http\Controllers;

use App\Models\CacahueteGame;
use App\Models\CacahueteParticipant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CacahueteController extends Controller
{
    public function index(): View
    {
        return view('admin.cacahuete.index', [
            'games' => CacahueteGame::query()->latest()->withCount('participants')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.cacahuete.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:1000'],
            'players' => ['required', 'string', 'max:10000'],
        ]);
        $players = $this->playersFromText($data['players']);

        if (count($players) < 2) {
            throw ValidationException::withMessages(['players' => 'Add at least two players to start a Cacahuète game.']);
        }

        DB::transaction(function () use ($data, $players): void {
            $game = CacahueteGame::create(['title' => $data['title'], 'message' => $data['message']]);
            $participants = collect($players)->map(fn (array $player) => $game->participants()->create($player));
            $recipientIds = $this->derangement($participants->pluck('id')->all());

            $participants->each(fn (CacahueteParticipant $participant, int $index) => $participant->update(['recipient_id' => $recipientIds[$index]]));
        });

        return redirect()->route('admin.cacahuete.index')->with('success', 'Cacahuète game created. Players can now reveal their gifts.');
    }

    public function toggle(CacahueteGame $cacahuete): RedirectResponse
    {
        $cacahuete->update(['is_active' => ! $cacahuete->is_active]);

        return back()->with('success', $cacahuete->is_active ? 'Game opened.' : 'Game closed.');
    }

    public function destroy(CacahueteGame $cacahuete): RedirectResponse
    {
        $cacahuete->delete();

        return back()->with('success', 'Cacahuète game removed.');
    }

    public function pick(Request $request, CacahueteParticipant $participant): RedirectResponse
    {
        abort_unless(strtolower($participant->email) === strtolower($request->user()->email) && $participant->game->is_active, 403);

        DB::transaction(function () use ($participant): void {
            $lockedParticipant = CacahueteParticipant::query()->lockForUpdate()->findOrFail($participant->id);

            if ($lockedParticipant->picked_at === null) {
                $lockedParticipant->update(['picked_at' => now()]);
            }
        });

        return redirect()->route('members.dashboard')->with('success', 'Your Cacahuète gift has been opened.');
    }

    /** @return list<array{name: string, email: string}> */
    private function playersFromText(string $playersText): array
    {
        $players = collect(preg_split('/\r\n|\r|\n/', $playersText))
            ->filter(fn (string $line): bool => filled(trim($line)))
            ->map(function (string $line): array {
                [$name, $email] = array_pad(array_map('trim', explode(',', $line, 2)), 2, '');

                if (blank($name) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw ValidationException::withMessages(['players' => 'Use one player per line in this format: Full name, email@example.com']);
                }

                return ['name' => $name, 'email' => strtolower($email)];
            })
            ->values();

        if ($players->pluck('email')->unique()->count() !== $players->count()) {
            throw ValidationException::withMessages(['players' => 'Each player must have a different email address.']);
        }

        return $players->all();
    }

    /** @param list<int> $participantIds @return list<int> */
    private function derangement(array $participantIds): array
    {
        do {
            $recipientIds = $participantIds;
            shuffle($recipientIds);
        } while (collect($participantIds)->values()->contains(
            fn (int $participantId, int $index): bool => $participantId === $recipientIds[$index],
        ));

        return $recipientIds;
    }
}
