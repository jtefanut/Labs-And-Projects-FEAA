<?php

namespace App\Http\Controllers;

use App\Models\HostingInquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HostingInquiryController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'company' => ['nullable', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        HostingInquiry::create($validated);

        return to_route('home')->with('success', 'Mesajul a ajuns la echipa noastra. Revenim catre tine in curand.');
    }
}
