<?php

namespace Touhidurabir\ModelSanitize\Tests;

use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;
use Orchestra\Testbench\TestCase;
use Illuminate\Database\Eloquent\Builder;
use Touhidurabir\ModelSanitize\Tests\App\User;
use Touhidurabir\ModelSanitize\Tests\App\State;
use Touhidurabir\ModelSanitize\Tests\App\Nation;
use Touhidurabir\ModelSanitize\Tests\App\Profile;
use Touhidurabir\ModelSanitize\Tests\App\Address;
use Touhidurabir\ModelSanitize\Facades\ModelSanitize;
use Touhidurabir\ModelSanitize\ModelSanitizeServiceProvider;

class LaravelIntegrationTest extends TestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {

        return [
            ModelSanitizeServiceProvider::class,
        ];
    }


    /**
     * Override application aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageAliases($app)
    {

        return [
            'ModelSanitize' => ModelSanitize::class,
        ];
    }


    /**
     * Define environment setup.
     *
     * @param  Illuminate\Foundation\Application $app
     * @return void
     */
    protected function defineEnvironment($app)
    {

        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('app.url', 'http://localhost/');
        $app['config']->set('app.debug', false);
        $app['config']->set('app.key', env('APP_KEY', '1234567890123456'));
        $app['config']->set('app.cipher', 'AES-128-CBC');
    }


    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations()
    {

        $this->loadMigrationsFrom(__DIR__ . '/App/database/migrations');

        $this->artisan('migrate', ['--database' => 'testbench'])->run();

        $this->beforeApplicationDestroyed(function () {
            $this->artisan('migrate:rollback', ['--database' => 'testbench'])->run();
        });
    }


    /**
     * Skip the test if the base eloquent builder method does not accept the values as Closure
     *
     * @param  string $method
     * @return void
     */
    protected function skipIfBuilderMethodNotAcceptClosureValues(string $method)
    {

        $type = (new ReflectionMethod(Builder::class, $method))->getParameters()[1]->getType();

        if (! $type || strpos((string) $type, 'Closure') === false) {

            $this->markTestSkipped("Eloquent Builder::{$method} does not accept Closure values on this Laravel version");
        }
    }


    /**
     * @test
     */
    #[Test]
    public function models_using_sanitizable_trait_will_have_access_to_static_methods()
    {

        $this->assertTrue(method_exists('Touhidurabir\\ModelSanitize\\Tests\\App\\User', 'sanitize'));
        $this->assertTrue(method_exists('Touhidurabir\\ModelSanitize\\Tests\\App\\User', 'gibberish'));
        $this->assertTrue(method_exists('Touhidurabir\\ModelSanitize\\Tests\\App\\Profile', 'sanitize'));
        $this->assertTrue(method_exists('Touhidurabir\\ModelSanitize\\Tests\\App\\Profile', 'gibberish'));
    }


    /**
     * @test
     */
    #[Test]
    public function sanitize_method_will_return_array()
    {

        $this->assertIsArray(User::sanitize(['email' => 'somemail@mail.com', 'password' => 'password']));
        $this->assertIsArray(Profile::sanitize(['first_name' => 'first_name', 'last_name' => 'last_name']));
    }


    /**
     * @test
     */
    #[Test]
    public function gibberish_method_will_return_array()
    {

        $this->assertIsArray(User::gibberish(['email' => 'somemail@mail.com', 'data' => 'data']));
        $this->assertIsArray(Profile::sanitize(['first_name' => 'first_name', 'bio' => 'some bio']));
    }


    /**
     * @test
     */
    #[Test]
    public function sanitize_method_will_return_proper_attributes()
    {

        $this->assertEquals(
            User::sanitize(['email' => 'somemail@mail.com', 'password' => 'password', 'data' => 'data']),
            ['email' => 'somemail@mail.com', 'password' => 'password']
        );

        $this->assertEquals(
            Profile::sanitize(['first_name' => 'first_name', 'bio' => 'some bio']),
            ['first_name' => 'first_name']
        );
    }


    /**
     * @test
     */
    #[Test]
    public function gibberish_method_will_return_non_useable_attributes()
    {

        $this->assertEquals(
            User::gibberish(['email' => 'somemail@mail.com', 'password' => 'password', 'data' => 'data']),
            ['data' => 'data']
        );

        $this->assertEquals(
            Profile::gibberish(['first_name' => 'first_name', 'bio' => 'some bio']),
            ['bio' => 'some bio']
        );
    }


    /**
     * @test
     */
    #[Test]
    public function it_will_properly_filter_at_time_create()
    {

        $user = User::create(['email' => 'somemail@mail.com', 'password' => 'password', 'data' => 'data']);
        $this->assertDatabaseHas('users', ['email' => 'somemail@mail.com', 'password' => 'password']);

        $profile = Profile::create(['first_name' => 'First Name', 'last_name' => 'Last _name', 'bio' => 'some bio']);
        $this->assertDatabaseHas('profiles', ['first_name' => 'First Name', 'last_name' => 'Last _name']);
    }


    /**
     * @test
     */
    #[Test]
    public function it_will_properly_filter_at_time_update()
    {

        $user = User::create(['email' => 'somemail@mail.com', 'password' => 'password']);
        $user->update(['email' => 'newemail@test.com', 'data' => 'some data']);

        $this->assertDatabaseHas('users', ['email' => 'newemail@test.com']);
    }


    /**
     * @test
     */
    #[Test]
    public function it_will_properly_sanitize_on_first_or_create()
    {

        $user = User::firstOrCreate(['email' => 'newmail001@mail.com'], ['email' => 'newmail001@mail.com', 'password' => 'password', 'data' => 'some new data']);

        $this->assertDatabaseHas('users', ['email' => 'newmail001@mail.com']);
    }


    /**
     * @test
     */
    #[Test]
    public function it_will_properly_sanitize_on_first_or_new()
    {

        $user = user::firstOrNew(['email' => 'somemail@mail.com', 'password' => 'password', 'data' => 'some data']);

        $this->assertTrue($user instanceof User);
    }


    /**
     * @test
     */
    #[Test]
    public function it_will_properly_sanitize_on_update_or_create()
    {

        $user = User::updateOrCreate(['email' => 'newtestmail001@test.mail'], [
            'email' => 'newtestmail001@test.mail',
            'password' => 'password',
            'data' => 'some data'
        ]);

        $this->assertDatabaseHas('users', ['email' => 'newtestmail001@test.mail']);

        $user = User::updateOrCreate(['email' => 'newtestmail001@test.mail'], [
            'email' => 'updatednewtestmail001@test.mail',
            'password' => 'new_password',
            'data' => 'some data'
        ]);

        $this->assertDatabaseHas('users', ['email' => 'updatednewtestmail001@test.mail']);
    }


    /**
     * @test
     */
    #[Test]
    public function it_will_properly_sanitize_on_first_or_create_with_closure_values()
    {

        $this->skipIfBuilderMethodNotAcceptClosureValues('firstOrCreate');

        $user = User::firstOrCreate(['email' => 'closuremail001@mail.com'], function () {
            return ['password' => 'password', 'data' => 'some new data'];
        });

        $this->assertDatabaseHas('users', ['email' => 'closuremail001@mail.com', 'password' => 'password']);
    }


    /**
     * @test
     */
    #[Test]
    public function it_will_properly_sanitize_on_first_or_new_with_closure_values()
    {

        $this->skipIfBuilderMethodNotAcceptClosureValues('firstOrNew');

        $user = User::firstOrNew(['email' => 'closuremail002@mail.com'], function () {
            return ['password' => 'password', 'data' => 'some new data'];
        });

        $this->assertEquals('password', $user->password);
        $this->assertArrayNotHasKey('data', $user->getAttributes());
    }


    /**
     * @test
     */
    #[Test]
    public function it_will_properly_sanitize_on_update_or_create_with_closure_values()
    {

        $this->skipIfBuilderMethodNotAcceptClosureValues('updateOrCreate');

        User::updateOrCreate(['email' => 'closuremail003@mail.com'], function () {
            return ['password' => 'password', 'data' => 'some data'];
        });

        $this->assertDatabaseHas('users', ['email' => 'closuremail003@mail.com', 'password' => 'password']);

        User::updateOrCreate(['email' => 'closuremail003@mail.com'], function () {
            return ['password' => 'new_password', 'data' => 'some data'];
        });

        $this->assertDatabaseHas('users', ['email' => 'closuremail003@mail.com', 'password' => 'new_password']);
    }


    /**
     * @test
     */
    #[Test]
    public function it_will_properly_sanitize_on_force_create()
    {

        $user = User::forceCreate([
            'email' => 'newtestmail002@test.mail',
            'password' => 'password',
            'data' => 'some data'
        ]);

        $this->assertDatabaseHas('users', ['email' => 'newtestmail002@test.mail']);
    }


    /**
     * @test
     */
    #[Test]
    public function it_will_not_fill_guarded_attributes()
    {

        $address = Address::create([
            'address_line_1'    => '5435 marthas vanieyard',
            'nation'            => 'US',
            'extras'            => 'some extra data',
        ]);

        $this->assertNull($address->extras);
    }


    /**
     * @test
     */
    #[Test]
    public function it_will_only_fill_fillable_attributes()
    {

        $nation = Nation::create([
            'name'          => 'United States',
            'code'          => 'US',
            'description'   => 'some extra description',
        ]);

        $this->assertNull($nation->description);
    }


    /**
     * @test
     */
    #[Test]
    public function it_will_honour_both_guarded_and_fillable_if_defined()
    {

        $state = State::create([
            'name'          => 'New York',
            'code'          => 'NY',
            'city_counts'   => 12,
            'description'   => 'some extra description',
        ]);

        $this->assertDatabaseHas('states', [
            'name' => 'New York',
            'code' => 'NY',
        ]);
        $this->assertNull($state->city_counts);
        $this->assertNull($state->description);
    }


    /**
     * @test
     */
    #[Test]
    public function the_sanitization_process_can_be_disabled_at_run_time()
    {

        $this->expectException(\Illuminate\Database\QueryException::class);

        User::disableSanitization();

        $user = User::create(['email' => 'somemail@mail.com', 'password' => 'password', 'data' => 'data']);
    }


    /**
     * @test
     */
    #[Test]
    public function the_disabled_sanitization_process_can_be_enabled_at_run_time()
    {

        User::disableSanitization();

        User::enableSanitization();

        $user = User::create(['email' => 'somemail@mail.com', 'password' => 'password', 'data' => 'data']);
        $this->assertDatabaseHas('users', ['email' => 'somemail@mail.com', 'password' => 'password']);
    }

}
