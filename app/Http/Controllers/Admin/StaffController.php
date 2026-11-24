<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Rules\PhoneNumber;
use App\Rules\ReservedUsername;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StaffController extends Controller
{
    public function index(): View
    {
        return view('admin.staff.index', [
            'members' => User::query()
                ->whereIn('role', [UserRole::Admin, UserRole::Manager, UserRole::Staff])
                ->latest()
                ->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.staff.form', [
            'member' => new User(['role' => UserRole::Staff, 'is_active' => true]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $data['password'] = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ])['password'];
        $data['email_verified_at'] = now();
        $data['phone_verified_at'] = now();

        $member = User::query()->create($data + ['is_active' => $request->boolean('is_active', true)]);

        ActivityLog::record('staff.created', $member, 'নতুন স্টাফ যোগ: @'.$member->username);

        return redirect()->route('admin.staff.index')->with('success', 'স্টাফ অ্যাকাউন্ট তৈরি হয়েছে।');
    }

    public function edit(User $staff): View
    {
        return view('admin.staff.form', ['member' => $staff]);
    }

    public function update(Request $request, User $staff): RedirectResponse
    {
        $data = $this->validated($request, $staff);
        $data['is_active'] = $request->boolean('is_active');

        if ($staff->is($request->user()) && $data['role'] !== UserRole::Admin->value) {
            return back()->withErrors(['role' => 'নিজের অ্যাডমিন রোল পরিবর্তন করা যাবে না।']);
        }

        if (filled($request->input('password'))) {
            $request->validate([
                'password' => ['confirmed', Password::min(8)->letters()->numbers()],
            ]);

            $data['password'] = $request->string('password')->toString();
        }

        $staff->forceFill($data)->save();

        ActivityLog::record('staff.updated', $staff, 'স্টাফ আপডেট: @'.$staff->username);

        return redirect()->route('admin.staff.index')->with('success', 'স্টাফ আপডেট হয়েছে।');
    }

    public function destroy(Request $request, User $staff): RedirectResponse
    {
        if ($staff->is($request->user())) {
            return back()->withErrors(['staff' => 'নিজের অ্যাকাউন্ট মুছে ফেলা যাবে না।']);
        }

        $staff->delete();

        ActivityLog::record('staff.deleted', null, 'স্টাফ মুছে ফেলা হয়েছে: @'.$staff->username);

        return back()->with('success', 'স্টাফ অ্যাকাউন্ট মুছে ফেলা হয়েছে।');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?User $member = null): array
    {
        $request->merge(['phone' => PhoneNumber::normalize($request->input('phone'))]);

        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'min:3', 'max:30', 'alpha_dash', 'lowercase', Rule::unique('users', 'username')->ignore($member?->id), new ReservedUsername],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($member?->id)],
            'phone' => ['required', 'string', 'max:20', new PhoneNumber, Rule::unique('users', 'phone')->ignore($member?->id)],
            'role' => ['required', 'in:admin,manager,staff'],
        ]);
    }
}
