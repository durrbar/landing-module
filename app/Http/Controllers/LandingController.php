<?php

namespace Modules\Landing\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Modules\Blog\Models\Post;
use Modules\Landing\Resources\PostResource;
use Modules\Ecommerce\Models\Product;
use Modules\Landing\Resources\ProductResource;
use Spatie\QueryBuilder\QueryBuilder;

class LandingController extends Controller
{
    protected const CACHE_LATEST_POSTS = 'api.v1.posts.latest';
    private const CACHE_FEATURED_PRODUCTS = 'api.v1.products.featured';
    private const CACHE_PUBLIC_PRODUCTS = 'public_products_';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cacheDuration = now()->addMinutes(config('cache.duration'));
        $latest = Cache::remember(self::CACHE_LATEST_POSTS, $cacheDuration, function () {
            return Post::select('id', 'slug', 'title', 'author_id', 'created_at')
                ->with(['author:id,first_name,last_name,name,avatar,avatar_url', 'cover'])
                ->latest()
                ->paginate(5); // Use pagination
        });

        $featured = Cache::remember(self::CACHE_FEATURED_PRODUCTS, $cacheDuration, function () {
            return Product::with('cover')->paginate(6); // Use pagination
        });

        return response()->json([
            'latest' => PostResource::collection($latest),
            'featureds' => ProductResource::collection($featured),
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function ecommerce()
    {
        $cacheKey = self::CACHE_PUBLIC_PRODUCTS;
        $cacheDuration = now()->addMinutes(config('cache.duration'));

        $products = Cache::remember($cacheKey, $cacheDuration, function () {
            return QueryBuilder::for(Product::query())
                ->allowedFields([
                    'id',
                    'slug',
                    'title',
                    'duration',
                    'author_id',
                    'created_at',
                    'total_views',
                    'total_shares'
                ])
                ->with('cover')
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->where('publish', 'published')
                ->limit(10)
                ->get(); // Convert to array to prevent serialization issues
        });

        return response()->json(ProductResource::collection($products)); // No need for ProductResource, pagination formats it already
    }
}
