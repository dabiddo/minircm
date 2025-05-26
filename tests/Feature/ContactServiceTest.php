<?php

use App\Models\Contact;
use App\Services\ContactService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

describe('ContactService', function () {
    it('can search contacts by valid email', function () {

        $contact = Contact::factory()->create([
            'email' => 'jdoe@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $request = Request::create('/contacts', 'GET', ['email' => 'jdoe@example.com']);
        $service = new ContactService;

        $results = $service->searchAndFilter($request);

        expect($results)->toHaveCount(1);
        expect($results->first()->id)->toBe($contact->id);
    });

    it('throws validation exception for invalid email', function () {

        $request = Request::create('/contacts', 'GET', ['email' => 'not-a-valid-email']);
        $service = new ContactService;

        expect(fn () => $service->searchAndFilter($request))
            ->toThrow(ValidationException::class);
    });

    it('can search contacts by first name', function () {

        $contact = Contact::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        Contact::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);

        $request = Request::create('/contacts', 'GET', ['first_name' => 'Jo']);
        $service = new ContactService;

        $results = $service->searchAndFilter($request);

        expect($results)->toHaveCount(1);
        expect($results->first()->first_name)->toBe('John');
    });
});
