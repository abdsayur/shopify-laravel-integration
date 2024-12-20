<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Signifly\Shopify\Support\Facades\Shopify;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    public function pushToShopify(Request $request)
    {
        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found']);
        }

        $shop = session('shop');
        $accessToken = session('shopify_access_token');

        $tokenValid = $this->checkShopifyToken($shop, $accessToken);
        if ($tokenValid !== true) {
            return $tokenValid; // If invalid, it'll redirect the user to reconnect
        }

        $response = Http::post("https://{$shop}/admin/api/2023-07/products.json?access_token={$accessToken}", [
            'product' => [
                'title' => $product->name,
                'body_html' => $product->description ?? '',
                'product_type' => 'default',
                'variants' => [
                    [
                        'price' => $product->price,
                        'sku' => '12345',
                        'inventory_quantity' => 100,
                        'inventory_management' => 'shopify',
                    ],
                ],
            ],
        ]);

        if ($response->successful()) {
            return response()->json([
                'success' => true,
                'message' => 'Product pushed successfully!',
                'link' => "https://{$shop}/admin/products/{$response->json()['product']['id']}" // Provide a link to view the product
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to push product.',
            'error' => $response->json(),
        ]);
    }

    public function checkShopifyToken($shop, $accessToken)
    {
        // Make a test request to check if the token is valid
        $response = Http::withToken($accessToken)->get("https://{$shop}/admin/api/2023-07/shop.json?access_token={$accessToken}");

        // If the response fails, the token might be expired or invalid
        if ($response->failed()) {
            // Token invalid or expired, prompt the user to reconnect
            session()->forget('shop');
            session()->forget('shopify_access_token');
            return redirect()->route('connect')->with('error', 'Your Shopify session has expired. Please reconnect your store.');
        }

        return true; // Token is valid
    }
}
