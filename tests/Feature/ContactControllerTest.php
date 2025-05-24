<?php

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('ContactController', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
    });

    it('should fetch a list of contacts', function () {
        Contact::factory()->count(10)->create();
        $response = $this->actingAs($this->user)->get('/api/v1/contacts');
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
        $contact = Contact::factory()->create();
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
        $contact = Contact::factory()->create();
        $updatedData = [
            'first_name' => 'Updated Name',
            'last_name' => 'Updated Last Name',
            'email' => 'updated@email.com',
            'phone_number' => '1234567890',
        ];

        $response = $this->actingAs($this->user)->put("/api/v1/contacts/{$contact->id}", $updatedData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('contacts', $updatedData);
    });

    it('should delete a contact', function () {
        $contact = Contact::factory()->create();
        $response = $this->actingAs($this->user)->delete("/api/v1/contacts/{$contact->id}");
        $response->assertStatus(204);
        $this->assertSoftDeleted($contact);
    });

    it('should return 404 for non-existent contact', function () {
        $response = $this->actingAs($this->user)->get('/api/v1/contacts/999999');
        $response->assertStatus(404);
    });
});
