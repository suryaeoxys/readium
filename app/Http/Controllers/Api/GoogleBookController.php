<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\ResponseBuilder;



class GoogleBookController extends Controller
{
    /**
     * Pages API
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
 
    public function bookSearch(Request $request)
    {
        $apiKey = env('GOOGLE_BOOK_API_KEY');

        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'key' => $apiKey,
            'q' => $request->search,
            // 'filter' => $request->filter// Example search query
        ]);

        return $response->json();
    }
    // public function bookSearch(Request $request)
    // {
    //     $apiKey = env('GOOGLE_BOOK_API_KEY');
    //     $sortBy = $request->sort_by ?? 'relevance'; // Default sort by relevance

    //     // Build query parameters
    //     $params = [
    //         'key' => $apiKey,
    //         'q' => $request->search,
    //         'orderBy' => $sortBy,
    //         'maxResults' => 10, // Number of results per page
    //     ];

    //     // Add filters for ratings and stores if provided
    //     if ($request->has('min_rating')) {
    //         $params['filter'] = 'minRating=' . $request->min_rating;
    //     }

    //     if ($request->has('top_stores')) {
    //         $params['printType'] = 'books'; // Only search for books
    //         $params['download'] = 'epub'; // Filter by epub format
    //     }

    //     $response = Http::get('https://www.googleapis.com/books/v1/volumes', $params);

    //     return $response->json();
    // }
    public function bookList(Request $request)
    {
        $apiKey = 'AIzaSyD7Qy42kK_VnLv6sfJTXWsXxmQFL3R6KmA';
        $response = Http::get('GET https://www.googleapis.com/books/v1/volumes/beSP5CCpiGUC', [
            'key' => $apiKey,
            // 'volumeId' => $request->volumeId, // Example search query
        ]);

        return $response->json();
    }
}
