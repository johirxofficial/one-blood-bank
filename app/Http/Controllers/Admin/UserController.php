<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Rules\PhoneNumber;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeManagement($request);

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:60'],
            'role' => ['nullable', Rule::enum(UserRole::class)],
        ]);

        $users = User::query()
            ->withCount(['ratings', 'receivedMessages'])
            ->when(filled($filters['q'] ?? null), function ($query) use ($filters): void {
                $term = $filters['q'];
                $query->where(fn ($query) => $query
                    ->where('name', 'like', "%{$term}%")
                    ->orWhere('username', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%"));
            })
            ->when(filled($filters['role'] ?? null), fn ($query) => $query->where('role', $filters['role']))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'filters'));
    }

    public function edit(Request $request, User $user): View
    {
        $this->authorizeTarget($request, $user);

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeTarget($request, $user);

        $request->merge(['phone' => PhoneNumber::normalize($request->input('phone'))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:20', new PhoneNumber, Rule::unique('users', 'phone')->ignore($user->id)],
            'role' => ['required', Rule::enum(UserRole::class)],
            'is_active' => ['boolean'],
            'ban_reason' => ['nullable', 'string', 'max:500'],
        ]);

        if ($user->is($request->user()) && $data['role'] !== UserRole::Admin->value) {
            return back()->withErrors(['role' => 'নিজের অ্যাডমিন রোল পরিবর্তন করা যাবে না।']);
        }

        if (! $request->user()->isAdmin() && $data['role'] !== $user->role->value) {
            return back()->withErrors(['role' => 'শুধুমাত্র অ্যাডমিন ইউজারের রোল পরিবর্তন করতে পারবেন।']);
        }

        if ($user->is($request->user()) && ! $request->boolean('is_active')) {
            return back()->withErrors(['is_active' => 'নিজের অ্যাকাউন্ট নিষ্ক্রিয় করা যাবে না।']);
        }

        $user->forceFill([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'role' => $data['role'],
            'is_active' => $request->boolean('is_active'),
            'banned_at' => $request->boolean('is_active') ? null : ($user->banned_at ?? now()),
            'ban_reason' => $data['ban_reason'] ?? null,
        ])->save();

        ActivityLog::record('user.updated', $user, 'ইউজার আপডেট: @'.$user->username.' ('.$user->role->value.')');

        return redirect()->route('admin.users.index')->with('success', 'ইউজার আপডেট হয়েছে।');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorizeTarget($request, $user);

        if ($user->is($request->user())) {
            return back()->withErrors(['user' => 'নিজের অ্যাকাউন্ট মুছে ফেলা যাবে না।']);
        }

        $user->donor?->delete();
        $user->delete();

        ActivityLog::record('user.deleted', null, 'ইউজার মুছে ফেলা হয়েছে: @'.$user->username);

        return back()->with('success', 'ইউজার মুছে ফেলা হয়েছে।');
    }

    private function authorizeManagement(Request $request): void
    {
        abort_unless($request->user()->canManageUsers(), 403, 'ইউজার ম্যানেজ করার অনুমতি নেই।');
    }

    private function authorizeTarget(Request $request, User $target): void
    {
        $this->authorizeManagement($request);

        abort_if(
            ! $request->user()->isAdmin() && $target->isStaff(),
            403,
            'অ্যাডমিন বা স্টাফ অ্যাকাউন্ট শুধুমাত্র অ্যাডমিন ম্যানেজ করতে পারবেন।',
        );
    }
}
