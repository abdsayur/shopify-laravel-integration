<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Kyon147\LaravelShopify\Facades\Shopify;
use Illuminate\Support\Facades\Crypt;

class ShopifyController extends Controller
{
    public function redirectToShopify(Request $request)
    {
        // Use a fixed shop or get it from the request
        $shop = 'derbascoyami.myshopify.com';

        if (!$shop) {
            return redirect('/')->with('error', 'Shop URL is required.');
        }

        // Correctly format the query parameters
        $query = http_build_query([
            'client_id'    => env('SHOPIFY_API_KEY'),   // Shopify API key
            'scope'        => 'read_products,write_products', // Required permissions
            'redirect_uri' => env('SHOPIFY_REDIRECT_URI'), // Set redirect URI in .env
            'state'        => csrf_token(),  // Use state for CSRF protection
            'grant_options[]' => 'per-user', // Optional: if you need offline access
        ]);

        // Redirect to the correct Shopify OAuth URL
        return redirect("https://{$shop}/admin/oauth/authorize?$query");
    }


    public function handleShopifyCallback(Request $request)
    {
        $shop = $request->get('shop');
        $code = $request->get('code');

        if (!$code) {
            return redirect('/')->with('error', 'Missing authorization code.');
        }

        $response = Http::post("https://{$shop}/admin/oauth/access_token", [
            'client_id' => env('SHOPIFY_API_KEY'),
            'client_secret' => env('SHOPIFY_API_SECRET'),
            'code' => $code,
        ]);

        if ($response->failed() || !isset($response->json()['access_token'])) {
            return redirect('/')->with('error', 'Failed to retrieve access token.');
        }


        $accessToken = $response->json()['access_token'];
        $encryptedAccessToken = Crypt::encryptString($accessToken);

        DB::table('shops')->updateOrInsert(
            ['shop' => $shop],
            [
                'access_token' => $encryptedAccessToken,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]
        );

        session([
            'shop' => $shop,
            'shopify_access_token' => $accessToken,
        ]);

        return redirect()->route('products.index')->with('success', 'Authentication successful!');
    }
}
