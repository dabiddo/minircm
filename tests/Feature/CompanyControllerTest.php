<?php

use App\Models\Company;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('CompanyController', function (): void {
    it('can list all companies', function () {
        $companies = Company::factory()->count(3)->create();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/v1/companies');

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                '*' => ['id', 'name', 'created_at', 'updated_at'],
            ]]);
    });

    it('can create a new company', function () {
        $companyData = [
            'name' => 'Test Company',
            'domain' => 'Test Domain',
        ];

        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/companies', $companyData);

        $response->assertStatus(201)
            ->assertJson(['data' => [
                'name' => $companyData['name'],
                'domain' => $companyData['domain'],
            ]]);

        $this->assertDatabaseHas('companies', $companyData);
    });

    it('can show a specific company', function () {
        $company = Company::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson("/api/v1/companies/{$company->id}");

        $response->assertStatus(200)
            ->assertJson(['data' => [
                'id' => $company->id,
                'name' => $company->name,
            ]]);
    });

    it('can update a company', function () {
        $company = Company::factory()->create();
        $user = User::factory()->create();

        $updatedData = [
            'name' => 'Updated Company Name',
            'domain' => 'Updated Domain',
        ];

        $response = $this->actingAs($user)->putJson("/api/v1/companies/{$company->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson(['data' => [
                'id' => $company->id,
                'name' => $updatedData['name'],
                'domain' => $updatedData['domain'],
            ]]);

        $this->assertDatabaseHas('companies', $updatedData);
    });

    it('can delete a company', function () {
        $company = Company::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/v1/companies/{$company->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted($company);
    });

    it('returns 404 when company not found', function () {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->getJson('/api/v1/companies/999');

        $response->assertStatus(404);
    });

    it('validates required fields when creating company', function () {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson('/api/v1/companies', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    });

    it('can show deleted company when withTrashed parameter is true', function () {
        $company = Company::factory()->create();
        $user = User::factory()->create();
        $company->delete();

        $response = $this->actingAs($user)->getJson('/api/v1/companies?withTrashed=true');
        $response->assertStatus(200);

        $responseData = $response->json('data.0');

        expect($responseData['id'])->toBe($company->id);
        expect($responseData['name'])->toBe($company->name);
        expect($responseData['domain'])->toBe($company->domain);
        expect($responseData['deleted_at'])->not->toBeNull();
    });

});

describe('Company Contact', function () {
    it('can attach an existing contact to a company', function () {

        $user = User::factory()->create();
        $company = Company::factory()->create();
        $contact = Contact::factory()->create();

        $payload = [
            'contact_id' => $contact->id,
        ];

        $response = $this->actingAs($user)
            ->postJson("/api/v1/companies/{$company->id}/contacts", $payload);

        $response->assertStatus(201)
            ->assertJson([
                'id' => $contact->id,
                'company_id' => $company->id,
            ]);

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'company_id' => $company->id,
        ]);
    });

    it('can create and attach a new contact to a company', function () {

        $user = User::factory()->create();
        $company = Company::factory()->create();

        $payload = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone_number' => '123-4567',
        ];

        $response = $this->actingAs($user)
            ->postJson("/api/v1/companies/{$company->id}/contacts", $payload);

        $response->assertStatus(201)
            ->assertJson([
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'company_id' => $company->id,
            ]);

        $this->assertDatabaseHas('contacts', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone_number' => '123-4567',
            'company_id' => $company->id,
        ]);
    });

    it('validates required fields when creating a new contact', function () {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $response = $this->actingAs($user)
            ->postJson("/api/v1/companies/{$company->id}/contacts", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'email', 'phone_number']);
    });
});
