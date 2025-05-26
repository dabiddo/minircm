<?php

use App\Models\Contact;
use App\Models\Deal;
use App\Services\DealsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

describe('DealsService', function () {
    it('returns filtered deals and throws exception for invalid status', function () {

        $contact = Contact::factory()->create();
        Deal::factory()->create([
            'contact_id' => $contact->id,
            'title' => 'Test Deal 1',
            'status' => 'open',
            'currency' => 'USD',
            'amount' => 1000,
        ]);

        Deal::factory()->create([
            'contact_id' => $contact->id,
            'title' => 'Test Deal 2',
            'status' => 'closed-won',
            'currency' => 'EUR',
            'amount' => 2000,
        ]);

        $service = new DealsService;

        $request = Request::create('/', 'GET', ['status' => 'open']);
        $results = $service->searchAndFilter($request);
        expect($results)->toHaveCount(1);

        $invalidRequest = Request::create('/', 'GET', ['status' => 'invalid-status']);
        expect(fn () => $service->searchAndFilter($invalidRequest))->toThrow(ValidationException::class);
    });

    it('filters deals by status', function () {

        $contact = Contact::factory()->create();
        Deal::factory()->create([
            'contact_id' => $contact->id,
            'title' => 'Test Deal 1',
            'status' => 'open',
            'currency' => 'USD',
            'amount' => 1000,
        ]);

        Deal::factory()->create([
            'contact_id' => $contact->id,
            'title' => 'Test Deal 2',
            'status' => 'closed-won',
            'currency' => 'EUR',
            'amount' => 2000,
        ]);

        $service = new DealsService;

        $request = Request::create('/', 'GET', ['status' => 'open']);

        $results = $service->searchAndFilter($request);

        expect($results)->toHaveCount(1);
        expect($results->first()->title)->toBe('Test Deal 1');
    });

    test('it filters deals by title', function () {

        $contact = Contact::factory()->create();
        Deal::factory()->create([
            'contact_id' => $contact->id,
            'title' => 'Test Deal 1',
            'status' => 'open',
            'currency' => 'USD',
            'amount' => 1000,
        ]);

        Deal::factory()->create([
            'contact_id' => $contact->id,
            'title' => 'Test Deal 2',
            'status' => 'closed-won',
            'currency' => 'EUR',
            'amount' => 2000,
        ]);

        $service = new DealsService;

        $request = Request::create('/', 'GET', ['title' => 'Deal 2']);

        $results = $service->searchAndFilter($request);

        expect($results)->toHaveCount(1);
        expect($results->first()->status)->toBe('closed-won');
    });
});
