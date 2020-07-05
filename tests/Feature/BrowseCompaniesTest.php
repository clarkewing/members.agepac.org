<?php

namespace Tests\Feature;

use App\Company;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class BrowseCompaniesTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();
    }

    /** @test */
    public function testGuestCannotBrowseCompanies()
    {
        Auth::logout();

        $this->showCompany()
            ->assertUnauthorized();
    }

    /** @test */
    public function testUserCanViewCompany()
    {
        $company = create(Company::class);

        $this->showCompany($company)
            ->assertJson($company->toArray());
    }

    /** @test */
    public function testUserCanGetIndexOfCompanies()
    {
        $companies = create(Company::class, [], 10);

        $this->get(route('companies.index'))
            ->assertViewIs('companies.index');

        $this->getJson(route('companies.index'))
            ->assertJson(['data' => $companies->toArray()]);
    }

    /**
     * @test
     * @group external-api
     * @group algolia-api
     */
    public function testUserCanSearchCompanies()
    {
        if (! config('scout.algolia.id')) {
            $this->markTestSkipped('Algolia is not configured.');
        }

        config(['scout.driver' => 'algolia']);

        $search = 'Foobar';

        create(Company::class, [], 2);
        create(Company::class, ['name' => "{$search} Inc"]);
        create(Company::class, ['name' => "{$search} Ltd"]);

        $maxTime = now()->addSeconds(20);

        do {
            sleep(.25);

            $results = $this->getJson(route('companies.index', ['query' => $search]))->json()['data'];
        } while (empty($results) && now()->lessThan($maxTime));

        $this->assertCount(2, $results);

        // Clean up index.
        Company::latest()->take(4)->unsearchable();
    }

    /**
     * Show the requested company
     *
     * @param  \App\Company  $company
     * @return \Illuminate\Testing\TestResponse
     */
    protected function showCompany(Company $company = null)
    {
        return $this->getJson(route(
            'companies.show',
            $company ?? create(Company::class)
        ));
    }
}
