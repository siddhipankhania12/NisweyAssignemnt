<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contacts = Contact::all();
        return view('contacts.index', compact('contacts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
        ]);
    
        Contact::create($request->only('name', 'phone'));
    
        return redirect()->route('contacts.index')->with('success', 'Contact added!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        //
    }

    public function import(Request $request)
    {
        $request->validate([
            'xml_file' => 'required|file|mimetypes:text/xml,application/xml',
        ]);

        $xml = simplexml_load_file($request->file('xml_file'));

        if (!isset($xml->contact)) {
            return back()->with('error', 'Invalid XML format.');
        }

        // Build a lookup of existing contacts to avoid querying multiple times
        $existing = Contact::all()->map(function ($contact) {
            return strtolower($contact->name) . '|' . $contact->phone;
        })->toArray();

        $imported = 0;

        foreach ($xml->contact as $item) {
            $name = trim((string) $item->name);
            $phone = trim((string) $item->phone);

            if (!$name || !$phone) continue;

            $key = strtolower($name) . '|' . $phone;

            if (!in_array($key, $existing)) {
                Contact::create([
                    'name' => $name,
                    'phone' => $phone,
                ]);
                $existing[] = $key;
                $imported++;
            }
        }

        return redirect()
            ->route('contacts.index')
            ->with('success', "$imported contact(s) imported successfully (duplicates skipped).");
    }

}
