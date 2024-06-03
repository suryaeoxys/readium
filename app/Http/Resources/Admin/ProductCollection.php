<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->map(function($data) {
            return [
                'id'            => $data->id,
                'author_id'   => $data->author_id,
                'publisher_id'   => $data->publisher_id,
                'name'          => (string)$data->name,
                'category_id'   => $data->category_id,
                'slug'           => (string)$data->slug,
                'main_image'         => !empty($data->main_image) ? url(config('app.product_image').'/'.$data->main_image) : '',
                'discription'           => (string)$data->discription,
                'no_of_page'      => $data->no_of_page,
                'isbn'       => (string)$data->isbn,
                'original_title'       => (string)$data->original_title,
                'year_of_publication'  => (string)$data->year_of_publication,
                'pdf_mp3' => !empty($data->pdf_mp3) ? url(config('app.media_content').'/'.$data->pdf_mp3) : '',
                'status'       => (string)$data->status,
            ];
        });
    }
}
