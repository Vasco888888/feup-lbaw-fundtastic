<?php

namespace App\Http\Controllers;

use App\Models\Admin;

class ContactController extends Controller
{
    public function show()
    {
        $admins = Admin::all(['admin_id', 'name', 'email']);
        return view('pages.contacts', compact('admins'));
    }
}
