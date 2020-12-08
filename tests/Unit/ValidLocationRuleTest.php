<?php

namespace Tests\Unit;

use App\Location;
use App\Rules\ValidLocation;
use Tests\TestCase;

class ValidLocationRuleTest extends TestCase
{
    /**
     * @var \App\Rules\ValidLocation
     */
    protected $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rule = new ValidLocation;
    }

    /** @test */
    public function testMustBeArray()
    {
        $this->assertRuleFails('foo');

        $this->assertRuleFails(42);

        $this->assertRuleFails(true);

        $this->assertRuleFails(null);
    }

    /** @test */
    public function testTypeIsRequired()
    {
        $this->assertRuleFails(['type' => null]);
    }

    /** @test */
    public function testTypeMustBeValid()
    {
        $this->assertRuleFails(['type' => 'foobar']);
    }

    /** @test */
    public function testAppropriateFieldsRequiredWithCountryType()
    {
        $this->assertRulePasses($this->location([
            'type' => 'country',
            'country' => 'France',
            'country_code' => 'FR',
        ]));

        $this->assertRuleFails($this->location([
            'type' => 'country',
            'country' => null,
        ]));

        $this->assertRuleFails($this->location([
            'type' => 'country',
            'country_code' => null,
        ]));
    }

    /** @test */
    public function testAppropriateFieldsRequiredWithCityType()
    {
        $this->assertRulePasses($this->location([
            'type' => 'city',
            'municipality' => 'Paris',
            'country' => 'France',
            'country_code' => 'FR',
        ]));

        $this->assertRuleFails($this->location([
            'type' => 'city',
            'municipality' => null,
        ]));

        $this->assertRuleFails($this->location([
            'type' => 'city',
            'country' => null,
        ]));

        $this->assertRuleFails($this->location([
            'type' => 'city',
            'country_code' => null,
        ]));
    }

    /** @test */
    public function testAppropriateFieldsRequiredWithAddressType()
    {
        $this->assertRulePasses($this->location([
            'type' => 'address',
            'street_line_1' => '172 boulevard Saint Germain',
            'municipality' => 'Paris',
            'country' => 'France',
            'country_code' => 'FR',
        ]));

        $this->assertRuleFails($this->location([
            'type' => 'address',
            'street_line_1' => null,
        ]));

        $this->assertRuleFails($this->location([
            'type' => 'address',
            'municipality' => null,
        ]));

        $this->assertRuleFails($this->location([
            'type' => 'address',
            'country' => null,
        ]));

        $this->assertRuleFails($this->location([
            'type' => 'address',
            'country_code' => null,
        ]));
    }

    /** @test */
    public function testNameRequiredWithAmenityTypes()
    {
        $this->assertRuleFails($this->location([
            'type' => 'busStop',
            'name' => null,
        ]));

        $this->assertRuleFails($this->location([
            'type' => 'trainStation',
            'name' => null,
        ]));

        $this->assertRuleFails($this->location([
            'type' => 'townhall',
            'name' => null,
        ]));

        $this->assertRuleFails($this->location([
            'type' => 'airport',
            'name' => null,
        ]));
    }

    /** @test */
    public function testPassesWithRandomLocation()
    {
        $this->assertRulePasses(Location::factory()->raw());
    }

    /**
     * Assert rule passes.
     *
     * @param  mixed  $value
     * @return void
     */
    protected function assertRulePasses($value): void
    {
        $this->assertTrue(
            $this->validateRule($value),
            'Failed asserting rule passes.'
        );

        $this->assertEmpty($this->rule->message());
    }

    /**
     * Assert rule fails.
     *
     * @param  mixed  $value
     * @return void
     */
    protected function assertRuleFails($value): void
    {
        $this->assertFalse(
            $this->validateRule($value),
            'Failed asserting rule fails.'
        );

        $this->assertNotEmpty($this->rule->message());
    }

    protected function validateRule($value)
    {
        return $this->rule->passes('test', $value);
    }

    protected function location(array $overrides = [])
    {
        return array_merge(Location::factory()->raw(), $overrides);
    }
}
