<?php

namespace Tests;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

trait TestHelpers
{
    /**
     * Create an authenticated user and return the JWT token
     */
    protected function createAuthenticatedUser(array $attributes = []): array
    {
        $user = User::factory()->create($attributes);
        $token = JWTAuth::fromUser($user);
        
        return ['user' => $user, 'token' => $token];
    }

    /**
     * Get authorization headers for authenticated requests
     */
    protected function getAuthHeaders(string $token): array
    {
        return ['Authorization' => 'Bearer ' . $token];
    }

    /**
     * Make an authenticated GET request
     */
    protected function authenticatedGet(string $uri, string $token = null): \Illuminate\Testing\TestResponse
    {
        if (!$token) {
            $auth = $this->createAuthenticatedUser();
            $token = $auth['token'];
        }

        return $this->withHeaders($this->getAuthHeaders($token))->getJson($uri);
    }

    /**
     * Make an authenticated POST request
     */
    protected function authenticatedPost(string $uri, array $data = [], string $token = null): \Illuminate\Testing\TestResponse
    {
        if (!$token) {
            $auth = $this->createAuthenticatedUser();
            $token = $auth['token'];
        }

        return $this->withHeaders($this->getAuthHeaders($token))->postJson($uri, $data);
    }

    /**
     * Make an authenticated PUT request
     */
    protected function authenticatedPut(string $uri, array $data = [], string $token = null): \Illuminate\Testing\TestResponse
    {
        if (!$token) {
            $auth = $this->createAuthenticatedUser();
            $token = $auth['token'];
        }

        return $this->withHeaders($this->getAuthHeaders($token))->putJson($uri, $data);
    }

    /**
     * Make an authenticated DELETE request
     */
    protected function authenticatedDelete(string $uri, array $data = [], string $token = null): \Illuminate\Testing\TestResponse
    {
        if (!$token) {
            $auth = $this->createAuthenticatedUser();
            $token = $auth['token'];
        }

        return $this->withHeaders($this->getAuthHeaders($token))->deleteJson($uri, $data);
    }

    /**
     * Assert that response has successful API structure
     */
    protected function assertSuccessfulResponse($response, $message = null): void
    {
        $response->assertStatus(200)
                ->assertJson(['success' => true]);

        if ($message) {
            $response->assertJson(['message' => $message]);
        }
    }

    /**
     * Assert that response has created API structure
     */
    protected function assertCreatedResponse($response, $message = null): void
    {
        $response->assertStatus(201)
                ->assertJson(['success' => true]);

        if ($message) {
            $response->assertJson(['message' => $message]);
        }
    }

    /**
     * Assert that response has validation errors
     */
    protected function assertValidationErrors($response, array $fields): void
    {
        $response->assertStatus(422);
        
        foreach ($fields as $field) {
            $response->assertJsonValidationErrors($field);
        }
    }

    /**
     * Assert that response is unauthorized
     */
    protected function assertUnauthorized($response): void
    {
        $response->assertStatus(401);
    }

    /**
     * Assert that response is not found
     */
    protected function assertNotFound($response): void
    {
        $response->assertStatus(404);
    }
}