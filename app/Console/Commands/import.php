<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class import extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import {--file= : The CSV file to import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fileName = $this->option('file');

        if (empty($fileName)) {
            $this->error('Please provide a file name using --file option');

            return Command::FAILURE;
        }

        if (! Storage::exists("files/{$fileName}")) {
            $this->FakeData();
            $this->error('CSV file Path not found!');

            return Command::FAILURE;
        }

        $path = Storage::path("files/{$fileName}");

        if (! File::exists($path)) {
            $this->error('CSV file not found!');

            return Command::FAILURE;
        }

        $handle = fopen($path, 'r');
        $chunkSize = 10;
        $chunkCount = 0;
        $lineCount = 0;
        $chunk = [];
        $headers = [];

        $this->info('Processing CSV in chunks of '.$chunkSize.':');

        // Read the headers first
        if (($headers = fgetcsv($handle)) !== false) {
            $lineCount++;
        }

        // Now process the rest of the file
        while (($line = fgetcsv($handle)) !== false) {
            $chunk[] = $line;
            $lineCount++;

            // When we reach the chunk size, process the chunk
            if (count($chunk) >= $chunkSize) {
                $chunkCount++;
                $this->processChunk($chunk, $chunkCount, $headers);
                $chunk = []; // Reset the chunk
            }
        }

        // Process any remaining lines that didn't make a full chunk
        if (! empty($chunk)) {
            $chunkCount++;
            $this->processChunk($chunk, $chunkCount, $headers);
        }

        fclose($handle);

        $this->info('CSV processing completed! Processed '.$lineCount.' lines in '.$chunkCount.' chunks.');

        return Command::SUCCESS;
    }

    /**
     * Process a chunk of CSV data
     *
     * @param  array  $chunk
     * @param  int  $chunkNumber
     */
    private function processChunk($chunk, $chunkNumber, $headers)
    {
        $this->line('');
        $this->line('--- Chunk #'.$chunkNumber.' ---');

        // Find indices for company name and domain in headers
        $nameIndex = array_search('company_name', $headers);
        $domainIndex = array_search('company_domain', $headers);
        $contactFirstName = array_search('contact_first_name', $headers);
        $contactLastname = array_search('contact_last_name', $headers);
        $contactEmail = array_search('contact_email', $headers);
        $contactPhone = array_search('contact_phone', $headers);
        $dealTitle = array_search('deal_name', $headers);
        $dealAmount = array_search('deal_amount', $headers);
        $dealCurrency = array_search('deal_currency', $headers);
        $dealStatus = array_search('deal_status', $headers);

        if ($nameIndex === false || $domainIndex === false) {
            $this->error('Required columns "company_name" and "company_domain" not found in CSV');

            return;
        }

        foreach ($chunk as $index => $line) {
            if (empty($line[$nameIndex]) || empty($line[$domainIndex]) || empty($line[$dealStatus])) {
                $this->warn('Line '.($index + 1).': Skipped due to missing company name or domain');

                continue;
            }

            try {
                $company = Company::firstOrCreate(
                    ['name' => $line[$nameIndex]],
                    ['domain' => $line[$domainIndex]]
                );

                $contact = Contact::firstOrCreate(['email' => $line[$contactEmail]], [
                    'company_id' => $company->id,
                    'first_name' => $line[$contactFirstName],
                    'last_name' => $line[$contactLastname],
                    'email' => $line[$contactEmail],
                    'phone_number' => $line[$contactPhone],

                ]);

                $deal = Deal::updateOrCreate(['title' => $line[$dealTitle]], [
                    'contact_id' => $contact->id,
                    'title' => $line[$dealTitle],
                    'amount' => $line[$dealAmount],
                    'currency' => $line[$dealCurrency],
                    'status' => $line[$dealStatus],
                ]);
            } catch (\Exception $e) {
                // $this->error('Error processing line '.($index + 1).': '.$e->getMessage());
                dd($line);
            }

        }
    }

    private function fakeData()
    {
        $companies = [];
        $faker = \Faker\Factory::create();

        // Generate 50 companies
        for ($i = 0; $i < 50; $i++) {
            // Generate company name
            $companyName = str_replace(',', '', $faker->company);
            $companyDomain = $faker->randomElement(['R&D', 'Robotics', 'A.I.', 'I.T']);

            // Generate between 2 and 5 contacts for each company
            $numberOfContacts = rand(2, 5);
            for ($j = 0; $j < $numberOfContacts; $j++) {
                $firstName = $faker->firstName;
                $lastName = $faker->lastName;
                $email = strtolower($firstName.'.'.$lastName.'@'.$companyDomain);
                $phone = $faker->phoneNumber;

                // Generate 1-3 random deals for each contact
                $numberOfDeals = rand(1, 3);
                for ($k = 0; $k < $numberOfDeals; $k++) {
                    $companies[] = [
                        'company_name' => $companyName,
                        'company_domain' => $companyDomain,
                        'contact_first_name' => $firstName,
                        'contact_last_name' => $lastName,
                        'contact_email' => $email,
                        'contact_phone' => $phone,
                        'title' => $faker->catchPhrase,
                        'amount' => $faker->numberBetween(1000, 100000),
                        'currency' => 'USD',
                        'status' => $faker->randomElement(['open', 'closed-won', 'closed-lost']),
                    ];
                }
            }
        }
        // Create CSV content
        $csvContent = "company_name,company_domain,contact_first_name,contact_last_name,contact_email,contact_phone,deal_name,deal_amount,deal_currency,deal_status\n";
        foreach ($companies as $company) {
            // dd($company);
            $csvContent .= $company['company_name'].','.$company['company_domain'].','.$company['contact_first_name'].','.$company['contact_last_name'].','.$company['contact_email'].','.$company['contact_phone'].','.$company['title'].','.$company['amount'].','.$company['currency'].','.$company['status']."\n";
        }

        // Store the CSV file
        Storage::put('files/data.csv', $csvContent);
    }
}
