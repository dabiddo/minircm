<?php

use App\Models\Contact;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('DealController', function () {

    beforeEach(function () {
        $this->user = User::factory()->create();
    });

    it('can list all the deals', function () {

        Deal::factory(5)->create();

        $response = $this->actingAs($this->user)->get('/api/v1/deals');
        $response->assertStatus(200)->assertJsonCount(5, 'data');
        $response->assertJsonStructure(['data' => [['id', 'title', 'contact_id', 'created_at', 'updated_at']]]);

    });

    it('should create a new deal', function () {
        $contact = Contact::factory()->create();
        $dealData = [
            'contact_id' => $contact->id,
            'title' => 'New Deal',
            'amount' => 1000,
            'currency' => 'USD',
            'status' => 'open',
        ];

        $response = $this->actingAs($this->user)->post('/api/v1/deals', $dealData);
        $response->assertStatus(201);
        $this->assertDatabaseHas('deals', $dealData);
    });

    it('list single deal', function () {
        $deal = Deal::factory()->create();
        $response = $this->actingAs($this->user)->get('/api/v1/deals/'.$deal->id);
        $response->assertStatus(200);
        $response->assertJson(['data' => ['id' => $deal->id, 'title' => $deal->title, 'contact_id' => $deal->contact_id]]);

    });

    it('updates a deal', function () {
        $deal = Deal::factory()->create();
        $updatedData = [
            'title' => 'Updated Deal Title',
            'status' => 'closed-won',
        ];

        $response = $this->actingAs($this->user)->put('/api/v1/deals/'.$deal->id, $updatedData);

        $response->assertStatus(200)->assertJson(['data' => $updatedData]);
    });

    it('soft deletes a Deal', function () {
        $deal = Deal::factory()->create();

        $response = $this->actingAs($this->user)->delete('/api/v1/deals/'.$deal->id);

        $response->assertStatus(204);
        $this->assertSoftDeleted($deal);
    });
});
