<?php
namespace MicroweberPackages\Modules\FacebookPixel\Http\Controllers;

use Illuminate\Http\Request;
use MicroweberPackages\Export\Formats\CsvExport;
use MicroweberPackages\Product\Models\Product;

class FacebookPixelExportController
{
    public function index(Request $request) {

        $exportFeedSecret = get_option('fb_pixel_export_feed_secret', 'facebook_pixel');
        if (empty($exportFeedSecret)) {
            $exportFeedSecret = rand(111111, 999999);
        }
        
        $feedSecretFromRequest = $request->get('feed_secret', false);
        if ($exportFeedSecret !== $feedSecretFromRequest) {
            return response('Unauthorized.', 401);
        }

        $getProducts = Product::active()->get();
        if ($getProducts->isEmpty()) {
            return response('No products found.', 404);
        }

        $exportProducts = [];
        foreach ($getProducts as $product) {

            $productPrice = $product->price;
            $productSalePrice = $product->price;
            if ($product->hasSpecialPrice()) {
                $productSalePrice = $product->special_price;
                if ($productPrice == 0)  {
                    $productPrice = $productSalePrice;
                }
            }

            $mainImage = '';
            $productImages = get_media('rel_id=' . $product->id);
            if (isset($productImages[0])) {
                $mainImage = $productImages[0]['filename'];
            }
            $availability = 'out of stock';
            if ($product->inStock) {
                $availability = 'in stock';
            }
            $exportProduct = [
                'id' => $product->id,
                'title' => $product->title,
                'description' => content_description($product->id),
                'link' => content_link($product->id),
                'image_link' => $mainImage,
                'availability'=> $availability,
                'brand' => '',
                'condition' => 'new',
                'price' => $productPrice,
                'sale_price' => $productSalePrice,
            ];

            $exportProducts[] = $exportProduct;
        }

        $csvExport = new CsvExport([
            'fb-feed' => $exportProducts,
        ]);
        $export = $csvExport->start();
        if (isset($export['files'][0]['filepath'])) {
            $file = $export['files'][0]['filepath'];
            if (is_file($file)) {
                $fileContent = file_get_contents($file);
                if ($fileContent) {
                    return response($fileContent, 200)
                        ->header('Content-Type', 'text/csv')
                        ->header('Content-Disposition', 'attachment; filename="fb-feed.csv"');
                }
            }
        }

        return response('No products found.', 404);

    }
}
