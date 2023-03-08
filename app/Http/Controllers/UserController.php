<?php

namespace App\Http\Controllers;

use App\Enums\ClientStep;
use App\Mail\NewUserMail;
use App\Models\Entities\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function sendUserEmail(Request $request): RedirectResponse
    {
        $email = $request->get('email');
        Mail::to($email)->send(new NewUserMail($email));

        return redirect()->route('register')->with('success', 'Email sent successfully!');
    }

    public function storeNewUser(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        $user = new User();

        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->password = bcrypt($request->get('password'));
        $user->address = $request->get('address');
        $user->telephone = $request->get('telephone');
        $user->country = $request->get('country');
        $this->role = null;/*Role::CLIENT->getTitle();*/
        $this->step = ClientStep::REGISTERED->value;

        $user->save();

        return redirect()->route('login')->with('success', 'You have enrolled successfully!');
    }

    public function updateUserRole(Request $request): RedirectResponse
    {
        $user = User::find($request->get('id'));
        $user->role = $request->get('role');
        $user->save();

        return redirect(route('admin.users'))->with('success', 'User role updated successfully!');
    }
}
