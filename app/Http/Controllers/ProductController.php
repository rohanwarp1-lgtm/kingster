<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use Session;
use App\Models\User;
use App\Models\Warranty;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\ProductName;

class ProductController extends Controller
{
    public function index(){
        $products = Product::where('is_deleted', 0)->orderBy('index')->get()->toArray();
        return view('admin.product', compact('products'));
    }

    public function updateIndexing(Request $request){
        $productIds = $request->input('product_indexes', []);

        foreach ($productIds as $position => $id) {
            Product::where('id', $id)->update(['index' => $position + 1]);
        }
        return response()->json(['success' => true, 'message' => 'Product order saved!']);
    }

    //
    // PRODUCT
    //
    public function addProduct(Request $request){
        $this->cleanUnusedProductImages();
        $product = null;
        return view('admin.add_product', compact('product'));
    }

    function cleanUnusedProductImages(){
        $usedImages = [];

        $products = DB::table('products')->select([
            'default_img',
            'img_1', 'img_2', 'img_3', 'img_4', 'img_5',
            'img_6', 'img_7', 'img_8', 'img_9', 'img_10'
        ])->get();

        foreach ($products as $product) {
            foreach ((array) $product as $imagePath) {
                if ($imagePath) {
                    $usedImages[] = basename($imagePath);
                }
            }
        }

        $directory = base_path('public/assets/products');
        File::ensureDirectoryExists($directory);
        $allFiles = File::files($directory);

        $deletedFiles = [];

        foreach ($allFiles as $file) {
            $fileName = $file->getFilename();

            if (!in_array($fileName, $usedImages)) {
                File::delete($file->getPathname());
                $deletedFiles[] = $fileName;
            }
        }

        return true;
    }

    public function saveProduct(Request $request){

        if ($request->id) {
            $product = Product::find($request->id);
            if (!$product) {
                return response()->json(['status' => 0, 'success' => false, 'message' => 'Product not found!']);
            }
        } else {
            $product = new Product();
            $product->created_by = Auth::id();
        }

        $product->product_name = $request->product_name;
        $product->offer_price = $request->offer_price;
        $product->original_price = $request->original_price;
        $product->features = $request->features;
        $product->description = $request->description;
        $product->rating = $request->rating;
        $product->review_count = $request->review_count;
        $product->sold_count = $request->sold_count;

        $default_img = 'default_img';
        if ($request->hasFile($default_img)) {
            if ($product->default_img && File::exists(base_path($product->default_img))) {
                File::delete(base_path($product->default_img));
            }

            $uniqueName = time() . '_' . mt_rand(100000, 999999);
            $image = $request->file($default_img);
            $imageName = $uniqueName . "_default_" . $image->getClientOriginalName();
            $destination = base_path('public/assets/products');
            File::ensureDirectoryExists($destination);
            $image->move($destination, $imageName);
            $product->default_img = 'public/assets/products/' . $imageName;
        } else if (!$product->exists) {
            $product->default_img = null;
        }

        for ($i = 1; $i <= 10; $i++) {
            $imgKey = 'img_' . $i;
            if ($request->hasFile($imgKey)) {
                if ($product->$imgKey && File::exists(base_path($product->$imgKey))) {
                    File::delete(base_path($product->$imgKey));
                }
                $image = $request->file($imgKey);
                $uniqueName = time() . '_' . mt_rand(100000, 999999);

                $imageName = $uniqueName . "_{$i}_" . $image->getClientOriginalName();
                $destination = base_path('public/assets/products');
                File::ensureDirectoryExists($destination);
                $image->move($destination, $imageName);
                $product->$imgKey = 'public/assets/products/' . $imageName;
            } else if (!$product->exists) {
                $product->$imgKey = null;
            }
        }

        if ($request->has_variant) {
            $product->variant_1_name = $request->variant_1_name ?? null;
            $product->variant_1_price = $request->variant_1_price ?? null;
            $product->variant_1_oprice = $request->variant_1_oprice ?? null;
            $product->variant_1_offer = $request->variant_1_offer ?? null;

            $product->variant_2_name = $request->variant_2_name ?? null;
            $product->variant_2_price = $request->variant_2_price ?? null;
            $product->variant_2_oprice = $request->variant_2_oprice ?? null;
            $product->variant_2_offer = $request->variant_2_offer ?? null;

            $product->variant_3_name = $request->variant_3_name ?? null;
            $product->variant_3_price = $request->variant_3_price ?? null;
            $product->variant_3_oprice = $request->variant_3_oprice ?? null;
            $product->variant_3_offer = $request->variant_3_offer ?? null;
        }

        $product->is_variant = ($request->variant_1_name || $request->variant_2_name || $request->variant_3_name) ? 1 : 0;
        $product->modified_by = Auth::id();

        $product->save();

        $saveIndex = Product::find($product->id);
        $saveIndex->index = $product->id;
        $saveIndex->save();

        $action = $request->id ? 'updated' : 'created';
        activity('product')->causedBy(auth()->user())->performedOn($product)->withProperties(['name' => $product->product_name])->log($action);

        $msg = $request->id ? 'Product updated successfully!' : 'Product saved successfully!';
        return response()->json(['status' => 1, 'success' => true, 'message' => $msg]);
    }

    public function productAjax(Request $request) {
        $searchColumns = [
            'products.product_name',
            'products.offer_price',
            'products.original_price',
            'products.rating',
            'products.review_count',
            'products.sold_count',
            'u1.username',
            'u2.username',
            DB::raw("DATE_FORMAT(products.created_at, '%d-%m-%Y')"),
            DB::raw("DATE_FORMAT(products.updated_at, '%d-%m-%Y')"),
        ];

        $sortingColumns = [
            0 => 'products.id', // Action column - not sortable but included for index match
            1 => 'products.product_name',
            2 => 'products.offer_price',
            3 => 'products.original_price',
            4 => 'products.rating',
            5 => 'products.review_count',
            6 => 'products.sold_count',
            7 => 'u1.username',
            8 => 'u2.username',
            9 => 'products.created_at',
            10 => 'products.updated_at',
        ];

        $selectColumns = [
            'products.id',
            'products.product_name',
            'products.offer_price',
            'products.original_price',
            'products.rating',
            'products.review_count',
            'products.sold_count',
            'u1.username as created_by',
            'u2.username as modified_by',
            'products.created_at',
            'products.updated_at',
            'products.is_deleted',
        ];

        $recordsTotal = Product::where('products.is_deleted', 0)->count();

        $query = Product::query();
        $query->select($selectColumns);
		$query->leftJoin('users as u1', 'u1.id', '=', 'products.created_by');
		$query->leftJoin('users as u2', 'u2.id', '=', 'products.modified_by');

        if(isset($request->status_filter)){
            $query->where('products.is_deleted', $request->status_filter);
        }

        if (isset($request['order'][0])) {
            $query->orderBy(
                $sortingColumns[$request['order'][0]['column']],
                $request['order'][0]['dir']
            );
        }
        if (!empty($request['search']['value'])) {
            $search = $request['search']['value'];
            $query->where(function ($q) use ($search, $searchColumns) {
                foreach ($searchColumns as $i => $column) {
                    $i === 0 ? $q->where($column, 'like', "%$search%")
                            : $q->orWhere($column, 'like', "%$search%");
                }
            });
        }
        $recordsFiltered = $query->count();
        $query->skip($request->start)->take($request->length != -1 ? $request->length : $recordsTotal);
        $data = $query->get();

        $viewData = [];
        foreach ($data as $item) {
            $row = [];

			$uiAction = '<ul class="list-inline font-size-20 contact-links mb-0">';
            if($item->is_deleted == 0){
                $uiAction .= '<li class="list-inline-item px-2 pos-middle"><a href="javascript:void(0);" onclick="productDelete(' . $item->id . ')"><i class="fe fe-trash"></i></a></li>';
            }else{
                $uiAction .= '<li class="list-inline-item px-2 pos-middle"><a href="javascript:void(0);" onclick="productRestore(' . $item->id . ')"><i class="fe fe-rotate-ccw"></i></a></li>';
            }
            $uiAction .= '<li class="list-inline-item px-2 pos-middle"><a href="' . url('admin/edit-product?id=' . $item->id) . '"><i class="fe fe-edit"></i></a></li>';
			$uiAction .= '</ul>';

			$row['action'] = $uiAction;
            $row['product_name'] = $item->product_name;
            $row['created_by'] = $item->created_by;
            $row['modified_by'] = $item->modified_by;
            $row['offer_price'] = $item->offer_price;
            $row['original_price'] = $item->original_price;
            $row['rating'] = $item->rating;
            $row['review_count'] = $item->review_count;
            $row['sold_count'] = $item->sold_count;
            $row['created_at'] = date('d-m-Y', strtotime($item->created_at));
            $row['updated_at'] = date('d-m-Y', strtotime($item->updated_at));

            $viewData[] = $row;
        }

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $viewData,
        ]);
    }

    public function deleteProduct(Request $request){
        try {
            $product = Product::find($request->id);
            if (!$product) {
                return response()->json(['status' => 0, 'success' => false, 'message' => 'Product not found!']);
            }

            $product->is_deleted = 1;
            $product->modified_by = Auth::id();
            $product->save();

            activity('product')->causedBy(auth()->user())->performedOn($product)->log('deleted');

            return response()->json(['status' => 1, 'success' => true, 'message' => 'Product deleted successfully!']);

        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'success' => false, 'message' => 'Error deleting product: ' . $e->getMessage()]);
        }
    }

    public function restoreProduct(Request $request){
        try {
            $product = Product::find($request->id);
            if (!$product) {
                return response()->json(['status' => 0, 'success' => false, 'message' => 'Product not found!']);
            }

            $product->is_deleted = 0;
            $product->modified_by = Auth::id();
            $product->save();

            activity('product')->causedBy(auth()->user())->performedOn($product)->log('updated');

            return response()->json(['status' => 1, 'success' => true, 'message' => 'Product restored successfully!']);

        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'success' => false, 'message' => 'Error restored product: ' . $e->getMessage()]);
        }
    }

    public function productDetails(Request $request){
        try {
            $product = Product::find($request->id);
            if (!$product) {
                return response()->json(['status' => 0, 'success' => false, 'message' => 'Product not found!']);
            }

            return response()->json(['status' => 1, 'success' => true, 'data' => $product]);

        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'success' => false, 'message' => 'Error fetching product details: ' . $e->getMessage()]);
        }
    }

    public function editProduct(Request $request){
        $product = Product::find($request->id);
        if (!$product) {
            return redirect()->route('product.index')->with('error', 'Product not found!');
        }
        return view('admin.add_product', compact('product'));
    }


    //
    // PRODUCT NAME
    //
    public function addProductName(){
        return view('admin.product_name');
    }

    public function saveProductName(Request $request){
        
        if ($request->product_name_id) {
            $productName = ProductName::find($request->product_name_id);
            if (!$productName) {
                return response()->json(['status' => 0, 'success' => false, 'message' => 'Product name not found!']);
            }
        } else {
            $productName = new ProductName();
            $productName->created_by = Auth::id();
        }

        $productName->name = $request->product_name;
        $productName->modified_by = Auth::id();
        $productName->save();

        $msg = $request->product_name_id ? 'Product name updated successfully!' : 'Product name saved successfully!';
        return response()->json(['status' => 1, 'success' => true, 'message' => $msg]);
    }

    public function productNameAjax(Request $request) {
        $searchColumns = [
            'product_names.name',
            'u1.username',
            'u2.username',
        ];

        $sortingColumns = [
            0 => 'product_names.id',
            1 => 'product_names.name',
            2 => 'u1.username',
            3 => 'u2.username',
        ];

        $selectColumns = [
            'product_names.id',
            'product_names.name',
            'product_names.is_deleted',
            'u1.username as created_by',
            'u2.username as modified_by',
        ];

        $recordsTotal = ProductName::where('product_names.is_deleted', 0)->count();

        $query = ProductName::query();
        $query->select($selectColumns);
        $query->leftJoin('users as u1', 'u1.id', '=', 'product_names.created_by');
        $query->leftJoin('users as u2', 'u2.id', '=', 'product_names.modified_by');

        if(isset($request->status_filter)){
            $query->where('product_names.is_deleted', $request->status_filter);
        }

        if (isset($request['order'][0])) {
            $query->orderBy(
                $sortingColumns[$request['order'][0]['column']],
                $request['order'][0]['dir']
            );
        }

        if (!empty($request['search']['value'])) {
            $search = $request['search']['value'];
            $query->where(function ($q) use ($search, $searchColumns) {
                foreach ($searchColumns as $i => $column) {
                    $i === 0 ? $q->where($column, 'like', "%$search%")
                            : $q->orWhere($column, 'like', "%$search%");
                }
            });
        }

        $recordsFiltered = $query->count();
        $query->skip($request->start)->take($request->length != -1 ? $request->length : $recordsTotal);
        $data = $query->get();

        $viewData = [];
        foreach ($data as $item) {
            $row = [];

            $uiAction = '<ul class="list-inline font-size-20 contact-links mb-0">';
                if($item->is_deleted == 0){
                    $uiAction .= '<li class="list-inline-item px-2 pos-middle"><a href="javascript:void(0);" onclick="productNameDelete(' . $item->id . ')"><i class="fe fe-trash"></i></a></li>';
                }else{
                    $uiAction .= '<li class="list-inline-item px-2 pos-middle"><a href="javascript:void(0);" onclick="productNameRestore(' . $item->id . ')"><i class="fe fe-rotate-ccw"></i></a></li>';
                }
                $uiAction .= '<li class="list-inline-item px-2 pos-middle"><a href="javascript:void(0);" onclick="productNameEdit(' . $item->id . ')"><i class="fe fe-edit"></i></a></li>';
            $uiAction .= '</ul>';

            $row['action'] = $uiAction;
            $row['name'] = $item->name;
            $row['created_by'] = $item->created_by;
            $row['modified_by'] = $item->modified_by;

            $viewData[] = $row;
        }

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $viewData,
        ]);
    }

    public function deleteProductName(Request $request){
        try {
            $productName = ProductName::find($request->id);
            if (!$productName) {
                return response()->json(['status' => 0, 'success' => false, 'message' => 'Product name not found!']);
            }

            $productName->is_deleted = 1;
            $productName->modified_by = Auth::id();
            $productName->save();

            return response()->json(['status' => 1, 'success' => true, 'message' => 'Product name deleted successfully!']);

        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'success' => false, 'message' => 'Error deleting product name: ' . $e->getMessage()]);
        }
    }

    public function restoreProductName(Request $request){
        try {
            $productName = ProductName::find($request->id);
            if (!$productName) {
                return response()->json(['status' => 0, 'success' => false, 'message' => 'Product name not found!']);
            }

            $productName->is_deleted = 0;
            $productName->modified_by = Auth::id();
            $productName->save();

            return response()->json(['status' => 1, 'success' => true, 'message' => 'Product name restored successfully!']);

        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'success' => false, 'message' => 'Error restoring product name: ' . $e->getMessage()]);
        }
    }

    public function productNameDetails(Request $request){
        try {
            $productName = ProductName::find($request->id);
            if (!$productName) {
                return response()->json(['status' => 0, 'success' => false, 'message' => 'Product name not found!']);
            }

            return response()->json(['status' => 1, 'success' => true, 'data' => $productName]);

        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'success' => false, 'message' => 'Error fetching product name details: ' . $e->getMessage()]);
        }
    }

    public function deleteProductImage(Request $request){
        $product = Product::findOrFail($request->product_id);
        $field = $request->image_field;

        if (!in_array($field, ['default_img', 'img_1', 'img_2', 'img_3', 'img_4', 'img_5', 'img_6'])) {
            return response()->json(['success' => false, 'message' => 'Invalid image field.']);
        }

        if ($product->$field) {
            @unlink(base_path($product->$field));
        }

        $product->$field = null;
        $product->save();

        return response()->json(['success' => true]);
    }
}
