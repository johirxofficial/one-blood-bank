<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DonorStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\DirectMessage;
use App\Models\User;
use App\Services\MailService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(): View
    {
        return view('admin.messages.index', [
            'messages' => DirectMessage::query()
                ->with(['sender', 'recipient'])
                ->latest()
                ->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.messages.create', [
            'users' => User::query()->orderBy('name')->take(200)->get(['id', 'name', 'username']),
        ]);
    }

    public function store(Request $request, MailService $mailer): RedirectResponse
    {
        $data = $request->validate([
            'audience' => ['required', 'in:all,donors,staff,user'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'subject' => ['required', 'string', 'max:160'],
            'body' => ['required', 'string', 'max:5000'],
            'channel' => ['required', 'in:email,internal,both'],
        ]);

        if ($data['audience'] === 'user' && blank($data['user_id'] ?? null)) {
            return back()->withErrors(['user_id' => 'একজন ইউজার নির্বাচন করুন।'])->withInput();
        }

        $recipients = $this->recipients($data['audience'], $data['user_id'] ?? null);
        $sent = 0;

        foreach ($recipients as $recipient) {
            $this->deliver($recipient, $data, $mailer);
            $sent++;
        }

        ActivityLog::record('message.sent', null, "কাস্টম মেসেজ পাঠানো হয়েছে: {$sent} জনকে — {$data['subject']}");

        return redirect()->route('admin.messages.index')
            ->with('success', "{$sent} জনকে মেসেজ পাঠানো হয়েছে।");
    }

    /**
     * @return Collection<int, User>
     */
    private function recipients(string $audience, ?int $userId): Collection
    {
        return User::query()
            ->where('is_active', true)
            ->when($audience === 'donors', fn (Builder $query) => $query->whereHas('donor', fn (Builder $query) => $query->where('status', DonorStatus::Approved)))
            ->when($audience === 'staff', fn (Builder $query) => $query->whereIn('role', [UserRole::Admin, UserRole::Manager, UserRole::Staff]))
            ->when($audience === 'user', fn (Builder $query) => $query->whereKey($userId))
            ->take(500)
            ->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function deliver(User $recipient, array $data, MailService $mailer): void
    {
        DirectMessage::query()->create([
            'sender_id' => auth()->id(),
            'recipient_id' => $recipient->id,
            'subject' => $data['subject'],
            'body' => $data['body'],
            'channel' => $data['channel'],
        ]);

        if (in_array($data['channel'], ['email', 'both'], true)) {
            $mailer->send(
                $recipient,
                $recipient->email,
                'custom',
                $data['subject'],
                $data['subject'],
                [$data['body']],
                route('dashboard'),
                'ড্যাশবোর্ডে যান',
            );
        }
    }
}
