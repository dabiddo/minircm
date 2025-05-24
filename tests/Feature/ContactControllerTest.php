<?php

use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('ContactController', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->company = Company::factory()->create();
    });

    it('should fetch a list of contacts', function () {
        Contact::factory()->count(10)->create();
        $response = $this->actingAs($this->user)->get("/api/v1/companies/{$this->company->id}/contacts");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'company_id',
                    'first_name',
                    'last_name',
                    'email',
                    'phone_number',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    });

    it('should fetch a single contact by ID', function () {
        $contact = Contact::factory()->create([
            'company_id' => $this->company->id,
        ]);
        $response = $this->actingAs($this->user)->get("/api/v1/contacts/{$contact->id}");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'company_id',
                'first_name',
                'last_name',
                'email',
                'phone_number',
                'created_at',
                'updated_at',
                'deleted_at',
            ],
        ]);
    });

    it('should update an existing contact', function () {
        $contact = Contact::factory()->create([
            'company_id' => $this->company->id,
        ]);
        $updatedData = [
            'first_name' => 'Updated Name',
            'last_name' => 'Updated Last Name',
            'email' => 'updated@email.com',
            'phone_number' => '1234567890',
        ];

        $response = $this->actingAs($this->user)->put("/api/v1/companies/{$this->company->id}/contacts/{$contact->id}", $updatedData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('contacts', $updatedData);
    });

    it('should delete a contact', function () {
        $contact = Contact::factory()->create([
            'company_id' => $this->company->id,
        ]);
        $response = $this->actingAs($this->user)->delete("/api/v1/contacts/{$contact->id}");
        $response->assertStatus(204);
        $this->assertSoftDeleted($contact);
    });

    it('should return 404 for non-existent contact', function () {
        $response = $this->actingAs($this->user)->get("/api/v1/companies/{$this->company->id}/contacts/999999");
        $response->assertStatus(404);
    });

    it('returns array of deals for a specific contact', function () {

        $contact = Contact::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $deals = Deal::factory()->count(3)->create([
            'contact_id' => $contact->id,
        ]);

        $response = $this->actingAs($this->user)->get("/api/v1/contacts/{$contact->id}/deals");

        $response->assertStatus(200);

        $response->assertJsonStructure(['data']);

        $this->assertCount(3, $response->json('data'));

        foreach ($deals as $deal) {
            $response->assertJsonPath('data.*.id', fn ($ids) => in_array($deal->id, $ids));
        }
    });

    it('can show deleted contacts when withTrashed parameter is true', function () {
        $contact = Contact::factory()->create();
        $user = User::factory()->create();
        $contact->delete();

        $response = $this->actingAs($user)->getJson('/api/v1/contacts?withTrashed=true');
        $response->assertStatus(200);

        $responseData = $response->json('data.0');

        expect($responseData['id'])->toBe($contact->id);
        expect($responseData['first_name'])->toBe($contact->first_name);
        expect($responseData['last_name'])->toBe($contact->last_name);
        expect($responseData['email'])->toBe($contact->email);
        expect($responseData['deleted_at'])->not->toBeNull();
    });
});
