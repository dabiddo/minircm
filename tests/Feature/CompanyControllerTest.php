<?php

use App\Models\Company;
use App\Models\User;

describe('CompanyController', function (): void {
    it('can list all companies', function () {
        $companies = Company::factory()->count(3)->create();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/v1/companies');

        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => ['id', 'name', 'created_at', 'updated_at'],
            ]);
    });

    it('can create a new company', function () {
        $companyData = [
            'name' => 'Test Company',
            'description' => 'Test Description',
            'address' => 'Test Address',
        ];

        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/companies', $companyData);

        $response->assertStatus(201)
            ->assertJson([
                'name' => $companyData['name'],
                'description' => $companyData['description'],
                'address' => $companyData['address'],
            ]);

        $this->assertDatabaseHas('companies', $companyData);
    });

    it('can show a specific company', function () {
        $company = Company::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson("/api/v1/companies/{$company->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $company->id,
                'name' => $company->name,
            ]);
    });

    it('can update a company', function () {
        $company = Company::factory()->create();
        $user = User::factory()->create();

        $updatedData = [
            'name' => 'Updated Company Name',
            'description' => 'Updated Description',
            'address' => 'Updated Address',
        ];

        $response = $this->actingAs($user)->putJson("/api/v1/companies/{$company->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson($updatedData);

        $this->assertDatabaseHas('companies', $updatedData);
    });

    it('can delete a company', function () {
        $company = Company::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/v1/companies/{$company->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
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

});
