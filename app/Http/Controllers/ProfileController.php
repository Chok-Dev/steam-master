<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    /**
     * Display the user's profile.
     */
    /**
     * Show the seller request form.
     */
    public function sellerRequest(Request $request): View
    {
        return view('profile.seller-request', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Store a seller request.
     */
    public function storeSellerRequest(Request $request): RedirectResponse
    {
        $request->validate([
            'seller_name' => 'required|string|max:255',
            'seller_description' => 'required|string|min:50',
            'accept_terms' => 'required|accepted',
        ]);

        // Update user with seller request information
        $user = $request->user();
        $user->seller_request_status = 'pending';
        $user->seller_request_at = now();
        $user->seller_details = [
            'name' => $request->seller_name,
            'description' => $request->seller_description,
        ];
        $user->save();

        // Notify admin (you would implement this part based on your notification system)
        // For example: Notification::send(User::where('role', 'admin')->get(), new SellerRequestNotification($user));

        return redirect()->route('profile.edit')
            ->with('success', 'คำขอเป็นผู้ขายถูกส่งเรียบร้อยแล้ว กรุณารอการอนุมัติจากผู้ดูแลระบบ');
    }
    public function show(Request $request, $username = null): View
    {
        // ถ้าไม่มีการระบุ username ให้แสดงโปรไฟล์ของผู้ใช้ที่เข้าสู่ระบบ
        if (is_null($username)) {
            return view('profile.show', [
                'user' => $request->user(),
            ]);
        }

        // แสดงโปรไฟล์ตาม username ที่ระบุ
        $user = \App\Models\User::where('name', $username)->firstOrFail();
        return view('profile.show', [
            'user' => $user,
        ]);
    }
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->safe()->except('avatar'));

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // Handle avatar upload if present
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::delete('public/' . $user->avatar);
            }

            // Store the new avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = str_replace('public/', '', $path);
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
