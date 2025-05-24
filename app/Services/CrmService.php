<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;

class CrmService
{
    public function createContact($data): Contact
    {
        return Contact::create($data);
    }

    public function attachContactToCompany(Company $company, Contact $contact)
    {
        $company->contacts()->attach($contact->id);
    }

    public function attachDealToContact(Deal $deal, Contact $contact)
    {
        $deal->contacts()->attach($contact->id);
    }
}
