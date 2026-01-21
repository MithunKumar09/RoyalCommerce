<?php

namespace App\Http\Controllers\Admin;

use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\Category;
use App\Models\Childcategory;
use App\Models\Currency;
use App\Models\Gallery;
use App\Models\Product;
use App\Models\Subcategory;
use Datatables;use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Image;
use Illuminate\Support\Facades\Log;
use Validator;

class ProductController extends AdminBaseController
{
    //*** JSON Request
    public function datatables(Request $request)
    {
        if ($request->type == 'all') {
            $datas = Product::whereProductType('normal')->latest('id')->get();
        } else if ($request->type == 'deactive') {
            $datas = Product::whereProductType('normal')->whereStatus(0)->latest('id')->get();
        }

        //--- Integrating This Collection Into Datatables
        return Datatables::of($datas)
            ->editColumn('name', function (Product $data) {
                $name = mb_strlen($data->name, 'UTF-8') > 50 ? mb_substr($data->name, 0, 50, 'UTF-8') . '...' : $data->name;
                $id = '<small>' . __("ID") . ': <a href="' . route('front.product', $data->slug) . '" target="_blank">' . sprintf("%'.08d", $data->id) . '</a></small>';
                $id3 = $data->type == 'Physical' ? '<small class="ml-2"> ' . __("SKU") . ': <a href="' . route('front.product', $data->slug) . '" target="_blank">' . $data->sku . '</a>' : '';
                return $name . '<br>' . $id . $id3 . $data->checkVendor();
            })
            ->editColumn('price', function (Product $data) {
                $price = $data->price * $this->curr->value;
                return \PriceHelper::showAdminCurrencyPrice($price);
            })
            ->editColumn('photo', function (Product $data) {
                $photo = $data->photo ? asset('assets/images/products/' . $data->photo) : asset('assets/images/noimage.png');
                return '<img src="' . $photo . '" alt="Image" class="img-thumbnail" style="width:80px">';
            })
            ->editColumn('stock', function (Product $data) {
                $stck = (string) $data->stock;
                if ($stck == "0") {
                    return __("Out Of Stock");
                } elseif ($stck == null) {
                    return __("Unlimited");
                } else {
                    return $data->stock;
                }

            })
            ->addColumn('status', function (Product $data) {
                $class = $data->status == 1 ? 'drop-success' : 'drop-danger';
                $s = $data->status == 1 ? 'selected' : '';
                $ns = $data->status == 0 ? 'selected' : '';
                return '<div class="action-list"><select class="process select droplinks ' . $class . '"><option data-val="1" value="' . route('admin-prod-status', ['id1' => $data->id, 'id2' => 1]) . '" ' . $s . '>' . __("Activated") . '</option><option data-val="0" value="' . route('admin-prod-status', ['id1' => $data->id, 'id2' => 0]) . '" ' . $ns . '>' . __("Deactivated") . '</option>/select></div>';
            })
            ->addColumn('action', function (Product $data) {
                $catalog = $data->type == 'Physical' ? ($data->is_catalog == 1 ? '<a href="javascript:;" data-href="' . route('admin-prod-catalog', ['id1' => $data->id, 'id2' => 0]) . '" data-toggle="modal" data-target="#catalog-modal" class="delete"><i class="fas fa-trash-alt"></i> ' . __("Remove Catalog") . '</a>' : '<a href="javascript:;" data-href="' . route('admin-prod-catalog', ['id1' => $data->id, 'id2' => 1]) . '" data-toggle="modal" data-target="#catalog-modal"> <i class="fas fa-plus"></i> ' . __("Add To Catalog") . '</a>') : '';
                return '<div class="godropdown"><button class="go-dropdown-toggle"> ' . __("Actions") . '<i class="fas fa-chevron-down"></i></button><div class="action-list"><a href="' . route('admin-prod-edit', $data->id) . '"> <i class="fas fa-edit"></i> ' . __("Edit") . '</a><a href="javascript" class="set-gallery" data-toggle="modal" data-target="#setgallery"><input type="hidden" value="' . $data->id . '"><i class="fas fa-eye"></i> ' . __("View Gallery") . '</a>' . $catalog . '<a data-href="' . route('admin-prod-feature', $data->id) . '" class="feature" data-toggle="modal" data-target="#modal2"> <i class="fas fa-star"></i> ' . __("Highlight") . '</a><a href="javascript:;" data-href="' . route('admin-prod-delete', $data->id) . '" data-toggle="modal" data-target="#confirm-delete" class="delete"><i class="fas fa-trash-alt"></i> ' . __("Delete") . '</a></div></div>';
            })
            ->rawColumns(['name', 'status', 'action','photo'])
            ->toJson(); //--- Returning Json Data To Client Side
    }

    //*** JSON Request
    public function catalogdatatables()
    {
        $datas = Product::where('is_catalog', '=', 1)->orderBy('id', 'desc');

        //--- Integrating This Collection Into Datatables
        return Datatables::of($datas)
            ->editColumn('name', function (Product $data) {
                $name = mb_strlen($data->name, 'UTF-8') > 50 ? mb_substr($data->name, 0, 50, 'UTF-8') . '...' : $data->name;
                $id = '<small>' . __("ID") . ': <a href="' . route('front.product', $data->slug) . '" target="_blank">' . sprintf("%'.08d", $data->id) . '</a></small>';
                $id3 = $data->type == 'Physical' ? '<small class="ml-2"> ' . __("SKU") . ': <a href="' . route('front.product', $data->slug) . '" target="_blank">' . $data->sku . '</a>' : '';
                return $name . '<br>' . $id . $id3 . $data->checkVendor();
            })
            ->editColumn('price', function (Product $data) {
                $price = $data->price * $this->curr->value;
                return \PriceHelper::showAdminCurrencyPrice($price);
            })
            ->editColumn('stock', function (Product $data) {
                $stck = (string) $data->stock;
                if ($stck == "0") {
                    return __("Out Of Stock");
                } elseif ($stck == null) {
                    return __("Unlimited");
                } else {
                    return $data->stock;
                }

            })
            ->addColumn('status', function (Product $data) {
                $class = $data->status == 1 ? 'drop-success' : 'drop-danger';
                $s = $data->status == 1 ? 'selected' : '';
                $ns = $data->status == 0 ? 'selected' : '';
                return '<div class="action-list"><select class="process select droplinks ' . $class . '"><option data-val="1" value="' . route('admin-prod-status', ['id1' => $data->id, 'id2' => 1]) . '" ' . $s . '>' . __("Activated") . '</option><option data-val="0" value="' . route('admin-prod-status', ['id1' => $data->id, 'id2' => 0]) . '" ' . $ns . '>' . __("Deactivated") . '</option>/select></div>';
            })
            ->addColumn('action', function (Product $data) {
                return '<div class="godropdown"><button class="go-dropdown-toggle">  ' . __("Actions") . '<i class="fas fa-chevron-down"></i></button><div class="action-list"><a href="' . route('admin-prod-edit', $data->id) . '"> <i class="fas fa-edit"></i> ' . __("Edit") . '</a><a href="javascript" class="set-gallery" data-toggle="modal" data-target="#setgallery"><input type="hidden" value="' . $data->id . '"><i class="fas fa-eye"></i> ' . __("View Gallery") . '</a><a data-href="' . route('admin-prod-feature', $data->id) . '" class="feature" data-toggle="modal" data-target="#modal2"> <i class="fas fa-star"></i> ' . __("Highlight") . '</a><a href="javascript:;" data-href="' . route('admin-prod-catalog', ['id1' => $data->id, 'id2' => 0]) . '" data-toggle="modal" data-target="#catalog-modal"><i class="fas fa-trash-alt"></i> ' . __("Remove Catalog") . '</a></div></div>';
            })
            ->rawColumns(['name', 'status', 'action'])
            ->toJson(); //--- Returning Json Data To Client Side
    }

    public function productscatalog()
    {
        return view('admin.product.catalog');
    }
    public function index()
    {
        return view('admin.product.index');
    }

    public function types()
    {
        return view('admin.product.types');
    }

    public function deactive()
    {
        return view('admin.product.deactive');
    }

    public function productsettings()
    {
        return view('admin.product.settings');
    }

    //*** GET Request
    public function create($slug)
    {
        $cats = Category::all();
        $sign = $this->curr;
        if ($slug == 'physical') {
            return view('admin.product.create.physical', compact('cats', 'sign'));
        } else if ($slug == 'digital') {
            return view('admin.product.create.digital', compact('cats', 'sign'));
        } else if (($slug == 'license')) {
            return view('admin.product.create.license', compact('cats', 'sign'));
        } else if (($slug == 'listing')) {
            return view('admin.product.create.listing', compact('cats', 'sign'));

        }
    }

    //*** GET Request
    public function status($id1, $id2)
    {
        $data = Product::findOrFail($id1);
        $data->status = $id2;
        $data->update();
        //--- Redirect Section
        $msg = __('Status Updated Successfully.');
        return response()->json($msg);
        //--- Redirect Section Ends
    }

    //*** POST Request
    public function uploadUpdate(Request $request, $id)
    {

        //--- Validation Section
        $rules = [
            'image' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }

        $data = Product::findOrFail($id);

        //--- Validation Section Ends
        $image = $request->image;
        list($type, $image) = explode(';', $image);
        list(, $image) = explode(',', $image);
        $image = base64_decode($image);
        $image_name = time() . Str::random(8) . '.png';
        $path = 'assets/images/products/' . $image_name;
        file_put_contents($path, $image);
        if ($data->photo != null) {
            if (file_exists(public_path() . '/assets/images/products/' . $data->photo)) {
                unlink(public_path() . '/assets/images/products/' . $data->photo);
            }
        }
        $input['photo'] = $image_name;
        $data->update($input);
        if ($data->thumbnail != null) {
            if (file_exists(public_path() . '/assets/images/thumbnails/' . $data->thumbnail)) {
                unlink(public_path() . '/assets/images/thumbnails/' . $data->thumbnail);
            }
        }

        $img = Image::make('assets/images/products/' . $data->photo)->resize(285, 285);
        $thumbnail = time() . Str::random(8) . '.jpg';
        $img->save('assets/images/thumbnails/' . $thumbnail);
        $data->thumbnail = $thumbnail;
        $data->update();
        return response()->json(['status' => true, 'file_name' => $image_name]);
    }

    //*** POST Request
    public function media360Upload(Request $request, $id)
    {
        $rules = [
            'media_360_frames' => 'required',
            'media_360_frames.*' => 'image',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }

        Product::findOrFail($id);

        $framesPath = public_path('assets/products_media/' . $id . '/360/frames');
        if (!file_exists($framesPath)) {
            mkdir($framesPath, 0755, true);
        }

        $mode = strtolower((string) $request->input('media_360_mode', 'append'));
        $files = $request->file('media_360_frames');
        $maxFrames = 360;

        $newExts = [];
        foreach ($files as $file) {
            $ext = strtolower($file->getClientOriginalExtension());
            if ($ext === '') {
                $ext = 'jpg';
            }
            $newExts[$ext] = true;
        }
        if (count($newExts) > 1) {
            return response()->json(array('errors' => [
                'media_360_frames' => __("All 360° frames must use the same image format.")
            ]));
        }
        $newExt = count($newExts) === 1 ? array_keys($newExts)[0] : null;
        $newCount = count($files);
        if ($mode === 'replace' && $newCount > $maxFrames) {
            return response()->json(array('errors' => [
                'media_360_frames' => __("Maximum 360° frames is :max.", ['max' => $maxFrames])
            ]));
        }

        if ($mode === 'replace') {
            // Replace mode: delete existing frames and renumber from 0001.
            $existingFiles = array_diff(scandir($framesPath), array('.', '..', 'manifest.json'));
            foreach ($existingFiles as $existingFile) {
                $existingPath = $framesPath . DIRECTORY_SEPARATOR . $existingFile;
                if (is_file($existingPath)) {
                    unlink($existingPath);
                }
            }

            foreach ($files as $index => $file) {
                $ext = strtolower($file->getClientOriginalExtension());
                if ($ext === '') {
                    $ext = 'jpg';
                }
                $fileName = str_pad($index + 1, 4, '0', STR_PAD_LEFT) . '.' . $ext;
                $file->move($framesPath, $fileName);
            }
        } else {
            // Append mode (default): do not delete existing frames. Find highest index first.
            $existingFiles = array_diff(scandir($framesPath), array('.', '..', 'manifest.json'));
            $maxIndex = 0;
            $existingExts = [];
            $indices = [];
            $invalidNames = [];
            foreach ($existingFiles as $existingFile) {
                if (!is_file($framesPath . DIRECTORY_SEPARATOR . $existingFile)) {
                    continue;
                }
                if (preg_match('/^(\d{4})\.(jpg|jpeg|png|webp)$/i', $existingFile, $matches)) {
                    $index = (int) $matches[1];
                    $indices[] = $index;
                    $existingExts[strtolower($matches[2])] = true;
                    if ($index > $maxIndex) {
                        $maxIndex = $index;
                    }
                } else {
                    $invalidNames[] = $existingFile;
                }
            }
            if (!empty($invalidNames)) {
                return response()->json(array('errors' => [
                    'media_360_frames' => __("Invalid existing frame filenames found. Use Replace to rebuild the sequence.")
                ]));
            }
            if (count($existingExts) > 1) {
                return response()->json(array('errors' => [
                    'media_360_frames' => __("Existing frames use mixed formats. Use Replace to rebuild with one format.")
                ]));
            }
            if (!empty($existingExts) && $newExt && !isset($existingExts[$newExt])) {
                return response()->json(array('errors' => [
                    'media_360_frames' => __("All frames must use the existing format.")
                ]));
            }
            if (!empty($indices)) {
                sort($indices);
                if ($indices[0] !== 1) {
                    return response()->json(array('errors' => [
                        'media_360_frames' => __("Existing frames are not a continuous sequence. Use Replace to rebuild.")
                    ]));
                }
                $prev = $indices[0];
                for ($i = 1; $i < count($indices); $i++) {
                    if ($indices[$i] !== $prev + 1) {
                        return response()->json(array('errors' => [
                            'media_360_frames' => __("Existing frames are not a continuous sequence. Use Replace to rebuild.")
                        ]));
                    }
                    $prev = $indices[$i];
                }
            }
            if (count($indices) + $newCount > $maxFrames) {
                return response()->json(array('errors' => [
                    'media_360_frames' => __("Maximum 360° frames is :max.", ['max' => $maxFrames])
                ]));
            }

            foreach ($files as $index => $file) {
                $ext = strtolower($file->getClientOriginalExtension());
                if ($ext === '') {
                    $ext = 'jpg';
                }
                // Append after the highest existing frame index.
                $frameIndex = $maxIndex + $index + 1;
                $fileName = str_pad($frameIndex, 4, '0', STR_PAD_LEFT) . '.' . $ext;

                // Avoid accidental overwrite if a file exists (e.g., concurrent uploads).
                while (file_exists($framesPath . DIRECTORY_SEPARATOR . $fileName)) {
                    $frameIndex++;
                    $fileName = str_pad($frameIndex, 4, '0', STR_PAD_LEFT) . '.' . $ext;
                }

                $file->move($framesPath, $fileName);
            }
        }

        // Regenerate manifest.json and preserve any existing metadata keys.
        $manifestPath = $framesPath . DIRECTORY_SEPARATOR . 'manifest.json';
        $existingManifest = [];
        if (file_exists($manifestPath)) {
            $existingManifest = json_decode(file_get_contents($manifestPath), true);
            if (!is_array($existingManifest)) {
                $existingManifest = [];
            }
        }

        $frames = array_diff(scandir($framesPath), array('.', '..', 'manifest.json'));
        $frames = array_filter($frames, function ($file) use ($framesPath) {
            return is_file($framesPath . DIRECTORY_SEPARATOR . $file);
        });
        $frames = array_values($frames);
        natsort($frames);
        $frames = array_values($frames);

        $frameUrls = array_map(function ($file) use ($id) {
            return asset('assets/products_media/' . $id . '/360/frames/' . $file);
        }, $frames);

        $manifestPayload = $existingManifest;
        $manifestPayload['frames'] = $frameUrls;
        file_put_contents($manifestPath, json_encode($manifestPayload));

        return response()->json(['frames' => $frameUrls]);
    }

    //*** GET Request
    public function media360Manifest($id)
    {
        Product::findOrFail($id);

        $framesPath = public_path('assets/products_media/' . $id . '/360/frames');
        if (!file_exists($framesPath)) {
            return response()->json(['frames' => []]);
        }

        $frames = array_diff(scandir($framesPath), array('.', '..', 'manifest.json'));
        $frames = array_filter($frames, function ($file) use ($framesPath) {
            return is_file($framesPath . DIRECTORY_SEPARATOR . $file);
        });
        $frames = array_values($frames);
        natsort($frames);
        $frames = array_values($frames);

        $frameUrls = array_map(function ($file) use ($id) {
            return asset('assets/products_media/' . $id . '/360/frames/' . $file);
        }, $frames);

        $manifestPath = $framesPath . DIRECTORY_SEPARATOR . 'manifest.json';
        file_put_contents($manifestPath, json_encode(['frames' => $frameUrls]));

        return response()->json(['frames' => $frameUrls]);
    }

    //*** POST Request
    public function media360Delete($id)
    {
        Product::findOrFail($id);

        $framesPath = public_path('assets/products_media/' . $id . '/360/frames');
        if (!file_exists($framesPath)) {
            return response()->json(['status' => true, 'frames' => []]);
        }

        $deleted = 0;
        $failed = 0;

        // Delete all files under framesPath (including manifest.json).
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($framesPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                if (!@rmdir($item->getPathname())) {
                    $failed++;
                }
                continue;
            }
            if (@unlink($item->getPathname())) {
                $deleted++;
            } else {
                $failed++;
            }
        }

        // Ensure frames directory exists for future uploads/manifest regeneration.
        if (!file_exists($framesPath)) {
            @mkdir($framesPath, 0755, true);
        }

        // Reset manifest to empty frames list to avoid stale previews.
        $manifestPath = $framesPath . DIRECTORY_SEPARATOR . 'manifest.json';
        @file_put_contents($manifestPath, json_encode(['frames' => []]));

        return response()->json([
            'status' => $failed === 0,
            'frames' => [],
            'deleted' => $deleted,
            'failed' => $failed,
        ]);
    }

    //*** POST Request
    public function store(Request $request)
    {
        //--- Validation Section
        $rules = [
            'photo' => 'required',
            'file' => 'mimes:zip',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends

        //--- Logic Section
        $data = new Product;
        $sign = $this->curr;
        $input = $request->all();

        // Check File
        if ($file = $request->file('file')) {
            $name = time() . \Str::random(8) . str_replace(' ', '', $file->getClientOriginalExtension());
            $file->move('assets/files', $name);
            $input['file'] = $name;
        }

        $image = $request->photo;
        list($type, $image) = explode(';', $image);
        list(, $image) = explode(',', $image);
        $image = base64_decode($image);
        $image_name = time() . Str::random(8) . '.png';
        $path = 'assets/images/products/' . $image_name;
        file_put_contents($path, $image);
        $input['photo'] = $image_name;

        if ($request->type == "Physical" || $request->type == "Listing") {
            $rules = ['sku' => 'min:8|unique:products'];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
            }

            if ($request->product_condition_check == "") {
                $input['product_condition'] = 0;
            }

            if ($request->preordered_check == "") {
                $input['preordered'] = 0;
            }

            if ($request->minimum_qty_check == "") {
                $input['minimum_qty'] = null;
            }

            if ($request->shipping_time_check == "") {
                $input['ship'] = null;
            }

            if (empty($request->stock_check)) {
                $input['stock_check'] = 0;
                $input['size'] = null;
                $input['size_qty'] = null;
                $input['size_price'] = null;
            } else {
                if (in_array(null, $request->size) || in_array(null, $request->size_qty) || in_array(null, $request->size_price)) {
                    $input['stock_check'] = 0;
                    $input['size'] = null;
                    $input['size_qty'] = null;
                    $input['size_price'] = null;
                } else {
                    $input['stock_check'] = 1;
                    $input['size'] = implode(',', $request->size);
                    $input['size_qty'] = implode(',', $request->size_qty);
                    $size_prices = $request->size_price;
                    $s_price = array();
                    foreach ($size_prices as $key => $sPrice) {
                        $s_price[$key] = $sPrice / $sign->value;
                    }
                    $input['size_price'] = implode(',', $s_price);
                }
            }

            if (empty($request->color_check)) {
                $input['color_all'] = null;
                $input['color_price'] = null;
            } else {
              
                $input['color_all'] = implode(',', $request->color_all);
            }

            if (empty($request->whole_check)) {
                $input['whole_sell_qty'] = null;
                $input['whole_sell_discount'] = null;
            } else {
                if (in_array(null, $request->whole_sell_qty) || in_array(null, $request->whole_sell_discount)) {
                    $input['whole_sell_qty'] = null;
                    $input['whole_sell_discount'] = null;
                } else {
                    $input['whole_sell_qty'] = implode(',', $request->whole_sell_qty);
                    $input['whole_sell_discount'] = implode(',', $request->whole_sell_discount);
                }
            }

            if ($request->mesasure_check == "") {
                $input['measure'] = null;
            }

        }

        if (empty($request->seo_check)) {
            $input['meta_tag'] = null;
            $input['meta_description'] = null;
        } else {
            if (!empty($request->meta_tag)) {
                $input['meta_tag'] = implode(',', $request->meta_tag);
            }
        }

        if ($request->type == "License") {

            if (in_array(null, $request->license) || in_array(null, $request->license_qty)) {
                $input['license'] = null;
                $input['license_qty'] = null;
            } else {
                $input['license'] = implode(',,', $request->license);
                $input['license_qty'] = implode(',', $request->license_qty);
            }

        }

        if (in_array(null, $request->features) || in_array(null, $request->colors)) {
            $input['features'] = null;
            $input['colors'] = null;
        } else {
            $input['features'] = implode(',', str_replace(',', ' ', $request->features));
            $input['colors'] = implode(',', str_replace(',', ' ', $request->colors));
        }

        if (!empty($request->tags)) {
            $input['tags'] = implode(',', $request->tags);
        }

        $input['price'] = ($input['price'] / $sign->value);
        $input['previous_price'] = ($input['previous_price'] / $sign->value);
        if ($request->cross_products) {
            $input['cross_products'] = implode(',', $request->cross_products);
        }

        $attrArr = [];
        if (!empty($request->category_id)) {
            $catAttrs = Attribute::where('attributable_id', $request->category_id)->where('attributable_type', 'App\Models\Category')->get();
            if (!empty($catAttrs)) {
                foreach ($catAttrs as $key => $catAttr) {
                    $in_name = $catAttr->input_name;
                    if ($request->has("$in_name")) {
                        $attrArr["$in_name"]["values"] = $request["$in_name"];
                        foreach ($request["$in_name" . "_price"] as $aprice) {
                            $ttt["$in_name" . "_price"][] = $aprice / $sign->value;
                        }
                        $attrArr["$in_name"]["prices"] = $ttt["$in_name" . "_price"];
                        if ($catAttr->details_status) {
                            $attrArr["$in_name"]["details_status"] = 1;
                        } else {
                            $attrArr["$in_name"]["details_status"] = 0;
                        }
                    }
                }
            }
        }

        if (!empty($request->subcategory_id)) {
            $subAttrs = Attribute::where('attributable_id', $request->subcategory_id)->where('attributable_type', 'App\Models\Subcategory')->get();
            if (!empty($subAttrs)) {
                foreach ($subAttrs as $key => $subAttr) {
                    $in_name = $subAttr->input_name;
                    if ($request->has("$in_name")) {
                        $attrArr["$in_name"]["values"] = $request["$in_name"];
                        foreach ($request["$in_name" . "_price"] as $aprice) {
                            $ttt["$in_name" . "_price"][] = $aprice / $sign->value;
                        }
                        $attrArr["$in_name"]["prices"] = $ttt["$in_name" . "_price"];
                        if ($subAttr->details_status) {
                            $attrArr["$in_name"]["details_status"] = 1;
                        } else {
                            $attrArr["$in_name"]["details_status"] = 0;
                        }
                    }
                }
            }
        }

        if (!empty($request->childcategory_id)) {
            $childAttrs = Attribute::where('attributable_id', $request->childcategory_id)->where('attributable_type', 'App\Models\Childcategory')->get();
            if (!empty($childAttrs)) {
                foreach ($childAttrs as $key => $childAttr) {
                    $in_name = $childAttr->input_name;
                    if ($request->has("$in_name")) {
                        $attrArr["$in_name"]["values"] = $request["$in_name"];
                        foreach ($request["$in_name" . "_price"] as $aprice) {
                            $ttt["$in_name" . "_price"][] = $aprice / $sign->value;
                        }
                        $attrArr["$in_name"]["prices"] = $ttt["$in_name" . "_price"];
                        if ($childAttr->details_status) {
                            $attrArr["$in_name"]["details_status"] = 1;
                        } else {
                            $attrArr["$in_name"]["details_status"] = 0;
                        }
                    }
                }
            }
        }

        if (empty($attrArr)) {
            $input['attributes'] = null;
        } else {
            $jsonAttr = json_encode($attrArr);
            $input['attributes'] = $jsonAttr;
        }


        // dd($input);

        // Save Data
        $data->fill($input)->save();

        // Set SLug
        $prod = Product::find($data->id);
        if ($prod->type != 'Physical' || $request->type != "Listing") {
            $prod->slug = Str::slug($data->name, '-') . '-' . strtolower(Str::random(3) . $data->id . Str::random(3));
        } else {
            $prod->slug = Str::slug($data->name, '-') . '-' . strtolower($data->sku);
        }

        // Set Thumbnail
        $img = Image::make('assets/images/products/' . $prod->photo)->resize(285, 285);
        $thumbnail = time() . Str::random(8) . '.jpg';
        $img->save('assets/images/thumbnails/' . $thumbnail);
        $prod->thumbnail = $thumbnail;
        $prod->update();

        // Add To Gallery If any
        $lastid = $data->id;
        if ($files = $request->file('gallery')) {
            foreach ($files as $key => $file) {
                if (in_array($key, $request->galval)) {
                    $gallery = new Gallery;
                    $name = time() . \Str::random(8) . str_replace(' ', '', $file->getClientOriginalExtension());
                    $file->move('assets/images/galleries', $name);
                    $gallery['photo'] = $name;
                    $gallery['product_id'] = $lastid;
                    $gallery->save();
                }
            }
        }
        //logic Section Ends

        //--- Redirect Section
        $msg = __("New Product Added Successfully.") . '<a href="' . route('admin-prod-index') . '">' . __("View Product Lists.") . '</a>';
        return response()->json($msg);
        //--- Redirect Section Ends
    }

    //*** GET Request
    public function import()
    {

        $cats = Category::all();
        $sign = $this->curr;
        return view('admin.product.productcsv', compact('cats', 'sign'));
    }

    //*** POST Request
    public function importSubmit(Request $request)
    {
        $log = "";
        //--- Validation Section
        $rules = [
            'csvfile' => 'required|mimes:csv,txt',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }

        $filename = '';
        if ($file = $request->file('csvfile')) {
            $filename = time() . '-' . $file->getClientOriginalExtension();
            $file->move('assets/temp_files', $filename);
        }

        $datas = "";

        $file = fopen(public_path('assets/temp_files/' . $filename), "r");
        $i = 1;

        while (($line = fgetcsv($file)) !== false) {

            if ($i != 1) {

                if (!Product::where('sku', $line[0])->exists()) {
                    //--- Validation Section Ends

                    //--- Logic Section
                    $data = new Product;
                    $sign = Currency::where('is_default', '=', 1)->first();

                    $input['type'] = 'Physical';
                    $input['sku'] = $line[0];

                    $input['category_id'] = null;
                    $input['subcategory_id'] = null;
                    $input['childcategory_id'] = null;

                    $mcat = Category::where(DB::raw('lower(name)'), strtolower($line[1]));
                    //$mcat = Category::where("name", $line[1]);

                    if ($mcat->exists()) {
                        $input['category_id'] = $mcat->first()->id;

                        if ($line[2] != "") {
                            $scat = Subcategory::where(DB::raw('lower(name)'), strtolower($line[2]));

                            if ($scat->exists()) {
                                $input['subcategory_id'] = $scat->first()->id;
                            }
                        }
                        if ($line[3] != "") {
                            $chcat = Childcategory::where(DB::raw('lower(name)'), strtolower($line[3]));

                            if ($chcat->exists()) {
                                $input['childcategory_id'] = $chcat->first()->id;
                            }
                        }

                        $input['photo'] = $line[5];
                        $input['name'] = $line[4];
                        $input['details'] = $line[6];
                        $input['color'] = $line[13];
                        $input['price'] = $line[7];
                        $input['previous_price'] = $line[8] != "" ? $line[8] : null;
                        $input['stock'] = $line[9];
                        $input['size'] = $line[10];
                        $input['size_qty'] = $line[11];
                        $input['size_price'] = $line[12];
                        $input['youtube'] = $line[15];
                        $input['policy'] = $line[16];
                        $input['meta_tag'] = $line[17];
                        $input['meta_description'] = $line[18];
                        $input['tags'] = $line[14];
                        $input['product_type'] = $line[19];
                        $input['affiliate_link'] = $line[20];
                        $input['slug'] = Str::slug($input['name'], '-') . '-' . strtolower($input['sku']);

                        $image_url = $line[5];

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_URL, $image_url);
                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
                        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                        curl_setopt($ch, CURLOPT_HEADER, true);
                        curl_setopt($ch, CURLOPT_NOBODY, true);

                        $content = curl_exec($ch);
                        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

                        $thumb_url = '';

                        if (strpos($contentType, 'image/') !== false) {
                            $fimg = Image::make($line[5])->resize(800, 800);
                            $fphoto = time() . Str::random(8) . '.jpg';
                            $fimg->save(public_path() . '/assets/images/products/' . $fphoto);
                            $input['photo'] = $fphoto;
                            $thumb_url = $line[5];
                        } else {
                            $fimg = Image::make(public_path() . '/assets/images/noimage.png')->resize(800, 800);
                            $fphoto = time() . Str::random(8) . '.jpg';
                            $fimg->save(public_path() . '/assets/images/products/' . $fphoto);
                            $input['photo'] = $fphoto;
                            $thumb_url = public_path() . '/assets/images/noimage.png';
                        }

                        $timg = Image::make($thumb_url)->resize(285, 285);
                        $thumbnail = time() . Str::random(8) . '.jpg';
                        $timg->save(public_path() . '/assets/images/thumbnails/' . $thumbnail);
                        $input['thumbnail'] = $thumbnail;

                        // Conert Price According to Currency
                        $input['price'] = ($input['price'] / $sign->value);
                        $input['previous_price'] = ($input['previous_price'] / $sign->value);

                        // Save Data
                        $data->fill($input)->save();

                    } else {
                        $log .= "<br>" . __('Row No') . ": " . $i . " - " . __('No Category Found!') . "<br>";
                    }

                } else {
                    $log .= "<br>" . __('Row No') . ": " . $i . " - " . __('Duplicate Product Code!') . "<br>";
                }
            }

            $i++;
        }
        fclose($file);

        //--- Redirect Section
        $msg = __('Bulk Product File Imported Successfully.') . $log;
        return response()->json($msg);
    }

    //*** GET Request
    public function edit($id)
    {
        $cats = Category::all();
        $data = Product::findOrFail($id);
        $sign = $this->curr;

        if ($data->type == 'Digital') {
            return view('admin.product.edit.digital', compact('cats', 'data', 'sign'));
        } elseif ($data->type == 'License') {
            return view('admin.product.edit.license', compact('cats', 'data', 'sign'));
        } elseif ($data->type == 'Listing') {
            return view('admin.product.edit.listing', compact('cats', 'data', 'sign'));
        } else {
            return view('admin.product.edit.physical', compact('cats', 'data', 'sign'));
        }

    }

    //*** POST Request
    public function update(Request $request, $id)
    {
        // return $request;
        //--- Validation Section
        $rules = [
            'file' => 'mimes:zip',
            // NOTE: Don't use `mimes:glb,gltf` here. On Windows/XAMPP many .glb uploads
            // are detected as `application/octet-stream` and fail MIME validation.
            // We validate as a file + max size here, and enforce the extension safely
            // in the dedicated 3D handling block below.
            'media_3d_model' => 'nullable|file|max:51200',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends

        //-- Logic Section
        $data = Product::findOrFail($id);
        $sign = $this->curr;
        $input = $request->all();

        //Check Types
        if ($request->type_check == 1) {
            $input['link'] = null;
        } else {
            if ($data->file != null) {
                if (file_exists(public_path() . '/assets/files/' . $data->file)) {
                    unlink(public_path() . '/assets/files/' . $data->file);
                }
            }
            $input['file'] = null;
        }

        // Check Physical
        if ($data->type == "Physical" || $data->type == "Listing") {
            //--- Validation Section
            $rules = ['sku' => 'min:8|unique:products,sku,' . $id];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
            }
            //--- Validation Section Ends

            // Check Condition
            if ($request->product_condition_check == "") {
                $input['product_condition'] = 0;
            }

            // Check Preorderd
            if ($request->preordered_check == "") {
                $input['preordered'] = 0;
            }

            // Check Minimum Qty
            if ($request->minimum_qty_check == "") {
                $input['minimum_qty'] = null;
            }

            // Check Shipping Time
            if ($request->shipping_time_check == "") {
                $input['ship'] = null;
            }

            // Check Size
            if (empty($request->stock_check)) {
                $input['stock_check'] = 0;
                $input['size'] = null;
                $input['size_qty'] = null;
                $input['size_price'] = null;
            } else {
                if (in_array(null, $request->size) || in_array(null, $request->size_qty) || in_array(null, $request->size_price)) {
                    $input['stock_check'] = 0;
                    $input['size'] = null;
                    $input['size_qty'] = null;
                    $input['size_price'] = null;
                } else {
                    $input['stock_check'] = 1;
                    $input['size'] = implode(',', $request->size);
                    $input['size_qty'] = implode(',', $request->size_qty);
                    $size_prices = $request->size_price;
                    $s_price = array();
                    foreach ($size_prices as $key => $sPrice) {
                        $s_price[$key] = $sPrice / $sign->value;
                    }
                    $input['size_price'] = implode(',', $s_price);
                }
            }

            if (empty($request->color_check)) {
                $input['color_all'] = null;
            } else {
                $input['color_all'] = implode(',', $request->color_all);
                // $color_prices = $request->color_price;
                // $c_price = array();
                // foreach ($color_prices as $key => $sPrice) {
                //     $c_price[$key] = $sPrice / $sign->value;
                // }
                // $input['color_price'] = implode(',', $c_price);
            }

            // Check Whole Sale
            if (empty($request->whole_check)) {
                $input['whole_sell_qty'] = null;
                $input['whole_sell_discount'] = null;
            } else {
                if (in_array(null, $request->whole_sell_qty) || in_array(null, $request->whole_sell_discount)) {
                    $input['whole_sell_qty'] = null;
                    $input['whole_sell_discount'] = null;
                } else {
                    $input['whole_sell_qty'] = implode(',', $request->whole_sell_qty);
                    $input['whole_sell_discount'] = implode(',', $request->whole_sell_discount);
                }
            }

            // Check Measure
            if ($request->measure_check == "") {
                $input['measure'] = null;
            }
        }

        // Check Seo
        if (empty($request->seo_check)) {
            $input['meta_tag'] = null;
            $input['meta_description'] = null;
        } else {
            if (!empty($request->meta_tag)) {
                $input['meta_tag'] = implode(',', $request->meta_tag);
            }
        }

        // Check License
        if ($data->type == "License") {

            if (!in_array(null, $request->license) && !in_array(null, $request->license_qty)) {
                $input['license'] = implode(',,', $request->license);
                $input['license_qty'] = implode(',', $request->license_qty);
            } else {
                if (in_array(null, $request->license) || in_array(null, $request->license_qty)) {
                    $input['license'] = null;
                    $input['license_qty'] = null;
                } else {
                    $license = explode(',,', $data->license);
                    $license_qty = explode(',', $data->license_qty);
                    $input['license'] = implode(',,', $license);
                    $input['license_qty'] = implode(',', $license_qty);
                }
            }

        }

        if (!in_array(null, $request->colors)) {
            $input['colors'] = implode(',', str_replace(',', ' ', $request->colors));
        } else {
            if (in_array(null, $request->features)) {
                $input['colors'] = null;
            } else {
                $colors = explode(',', $data->colors);
                $input['colors'] = implode(',', $colors);
            }
        }

        if (!in_array(null, $request->features) && !in_array(null, $request->colors)) {
            $input['features'] = implode(',', str_replace(',', ' ', $request->features));
        } else {
            if (in_array(null, $request->features) || in_array(null, $request->colors)) {
                $input['features'] = null;
            } else {
                $features = explode(',', $data->features);
                $input['features'] = implode(',', $features);
            }
        }

        if (!empty($request->tags)) {
            $input['tags'] = implode(',', $request->tags);
        }
        if (empty($request->tags)) {
            $input['tags'] = null;
        }

        $input['price'] = $input['price'] / $sign->value;
        $input['previous_price'] = $input['previous_price'] / $sign->value;

        // store filtering attributes for physical product
        $attrArr = [];
        if (!empty($request->category_id)) {
            $catAttrs = Attribute::where('attributable_id', $request->category_id)->where('attributable_type', 'App\Models\Category')->get();
            if (!empty($catAttrs)) {
                foreach ($catAttrs as $key => $catAttr) {
                    $in_name = $catAttr->input_name;
                    if ($request->has("$in_name")) {
                        $attrArr["$in_name"]["values"] = $request["$in_name"];
                        foreach ($request["$in_name" . "_price"] as $aprice) {
                            $ttt["$in_name" . "_price"][] = $aprice / $sign->value;
                        }
                        $attrArr["$in_name"]["prices"] = $ttt["$in_name" . "_price"];
                        if ($catAttr->details_status) {
                            $attrArr["$in_name"]["details_status"] = 1;
                        } else {
                            $attrArr["$in_name"]["details_status"] = 0;
                        }
                    }
                }
            }
        }

        if (!empty($request->subcategory_id)) {
            $subAttrs = Attribute::where('attributable_id', $request->subcategory_id)->where('attributable_type', 'App\Models\Subcategory')->get();
            if (!empty($subAttrs)) {
                foreach ($subAttrs as $key => $subAttr) {
                    $in_name = $subAttr->input_name;
                    if ($request->has("$in_name")) {
                        $attrArr["$in_name"]["values"] = $request["$in_name"];
                        foreach ($request["$in_name" . "_price"] as $aprice) {
                            $ttt["$in_name" . "_price"][] = $aprice / $sign->value;
                        }
                        $attrArr["$in_name"]["prices"] = $ttt["$in_name" . "_price"];
                        if ($subAttr->details_status) {
                            $attrArr["$in_name"]["details_status"] = 1;
                        } else {
                            $attrArr["$in_name"]["details_status"] = 0;
                        }
                    }
                }
            }
        }
        if (!empty($request->childcategory_id)) {
            $childAttrs = Attribute::where('attributable_id', $request->childcategory_id)->where('attributable_type', 'App\Models\Childcategory')->get();
            if (!empty($childAttrs)) {
                foreach ($childAttrs as $key => $childAttr) {
                    $in_name = $childAttr->input_name;
                    if ($request->has("$in_name")) {
                        $attrArr["$in_name"]["values"] = $request["$in_name"];
                        foreach ($request["$in_name" . "_price"] as $aprice) {
                            $ttt["$in_name" . "_price"][] = $aprice / $sign->value;
                        }
                        $attrArr["$in_name"]["prices"] = $ttt["$in_name" . "_price"];
                        if ($childAttr->details_status) {
                            $attrArr["$in_name"]["details_status"] = 1;
                        } else {
                            $attrArr["$in_name"]["details_status"] = 0;
                        }
                    }
                }
            }
        }

        if (empty($attrArr)) {
            $input['attributes'] = null;
        } else {
            $jsonAttr = json_encode($attrArr);
            $input['attributes'] = $jsonAttr;
        }
        if ($request->cross_products) {
            $input['cross_products'] = implode(',', $request->cross_products);
        }
        $mediaExtra = json_decode($data->media_extra, true);
        if (!is_array($mediaExtra)) {
            $mediaExtra = [];
        }
        $updateWarnings = [];

        $framesDir = public_path('assets/products_media/' . $data->id . '/360/frames');
        $manifestPath = $framesDir . '/manifest.json';
        $manifestUrl = null;
        $frameCount = 0;
        $zeroPad = false;
        $startFrame = 1;

        if (file_exists($manifestPath)) {
            $manifestData = json_decode(file_get_contents($manifestPath), true);
            if (isset($manifestData['frames']) && is_array($manifestData['frames'])) {
                $frameCount = count($manifestData['frames']);
                $zeroPad = $frameCount > 0;
                foreach ($manifestData['frames'] as $frameUrl) {
                    $filename = basename(parse_url($frameUrl, PHP_URL_PATH));
                    if (!preg_match('/^0+\d+\./', $filename)) {
                        $zeroPad = false;
                        break;
                    }
                }
            }
            $manifestUrl = asset('assets/products_media/' . $data->id . '/360/frames/manifest.json');
        }

        $hasV360Update = $request->has('media_360_enabled') || file_exists($manifestPath) || array_key_exists('v360', $mediaExtra);
        if ($hasV360Update) {
            $mediaExtra['v360'] = [
                'enabled' => $request->has('media_360_enabled') ? 1 : 0,
                'frame_count' => $frameCount,
                'base_path' => 'assets/products_media/' . $data->id . '/360/frames/',
                'zero_pad' => $zeroPad,
                'start_frame' => $startFrame,
                'manifest' => $manifestUrl,
            ];
            $input['media_extra'] = json_encode($mediaExtra);
        }

        $hasHotspotUpdate = $request->has('media_hotspot_enabled') || $request->has('media_hotspot_base') ||
            $request->has('media_hotspot_label') || $request->has('media_hotspot_description') ||
            $request->has('media_hotspot_x') || $request->has('media_hotspot_y') || array_key_exists('hotspots', $mediaExtra);
        if ($hasHotspotUpdate) {
            $labels = $request->input('media_hotspot_label', []);
            $descriptions = $request->input('media_hotspot_description', []);
            $xs = $request->input('media_hotspot_x', []);
            $ys = $request->input('media_hotspot_y', []);
            $types = $request->input('media_hotspot_type', []);
            $targets = $request->input('media_hotspot_target', []);
            $frames = $request->input('media_hotspot_frame', []);
            $ids = $request->input('media_hotspot_id', []);
            $deleteFlags = $request->input('media_hotspot_image_delete', []);
            $x3ds = $request->input('media_hotspot_x3d', []);
            $y3ds = $request->input('media_hotspot_y3d', []);
            $z3ds = $request->input('media_hotspot_z3d', []);

            $count = max(count($labels), count($descriptions), count($xs), count($ys), count($types), count($targets), count($frames));
            if ($count > 50) {
                $updateWarnings[] = __("Hotspot limit is 50. Extra items were ignored.");
                $count = 50;
            }

            $existingItems = [];
            if (isset($mediaExtra['hotspots']['items']) && is_array($mediaExtra['hotspots']['items'])) {
                foreach ($mediaExtra['hotspots']['items'] as $item) {
                    if (!empty($item['id'])) {
                        $existingItems[$item['id']] = $item;
                    }
                }
            }

            $items = [];
            $seenIds = [];
            $baseImage = (string) $request->input('media_hotspot_base', '');
            $hasBaseImage = !empty($baseImage);
            $model3dExists = false;
            if ($request->hasFile('media_3d_model')) {
                $model3dExists = true;
            } elseif (!empty($mediaExtra['model3d']['src'])) {
                $existingModel = $mediaExtra['model3d']['src'];
                $existingPath = parse_url($existingModel, PHP_URL_PATH);
                if ($existingPath) {
                    $model3dExists = file_exists(public_path(ltrim($existingPath, '/')));
                }
            }

            for ($i = 0; $i < $count; $i++) {
                $label = isset($labels[$i]) ? (string) $labels[$i] : '';
                $description = isset($descriptions[$i]) ? (string) $descriptions[$i] : '';
                $type = isset($types[$i]) ? (string) $types[$i] : 'text';
                $target = isset($targets[$i]) ? (string) $targets[$i] : 'image';
                if ($target !== 'image' && $target !== 'frame360' && $target !== 'model3d') {
                    $target = 'image';
                }

                if ($target === 'image' && !$hasBaseImage) {
                    $warning = __("Hotspot skipped: base image missing.");
                    $updateWarnings[] = $warning;
                    Log::warning($warning, ['product_id' => $data->id]);
                    continue;
                }
                if ($target === 'frame360' && $frameCount < 1) {
                    $warning = __("360° hotspot skipped: no frames available.");
                    $updateWarnings[] = $warning;
                    Log::warning($warning, ['product_id' => $data->id]);
                    continue;
                }
                if ($target === 'model3d' && !$model3dExists) {
                    $warning = __("3D hotspot skipped: model file missing.");
                    $updateWarnings[] = $warning;
                    Log::warning($warning, ['product_id' => $data->id]);
                    continue;
                }

                $frameValue = null;
                if ($target === 'frame360') {
                    $frameValue = isset($frames[$i]) ? (int) $frames[$i] : null;
                    if ($frameValue < 1 || $frameValue > $frameCount) {
                        $warning = __("360° hotspot skipped: frame out of range.");
                        $updateWarnings[] = $warning;
                        Log::warning($warning, ['product_id' => $data->id, 'frame' => $frameValue]);
                        continue;
                    }
                }

                $rawX = isset($xs[$i]) ? (float) $xs[$i] : null;
                $rawY = isset($ys[$i]) ? (float) $ys[$i] : null;

                if ($target === 'model3d') {
                    $x3d = isset($x3ds[$i]) ? $x3ds[$i] : null;
                    $y3d = isset($y3ds[$i]) ? $y3ds[$i] : null;
                    $z3d = isset($z3ds[$i]) ? $z3ds[$i] : null;
                    if (!is_numeric($x3d) || !is_numeric($y3d) || !is_numeric($z3d)) {
                        $warning = __("3D hotspot skipped: invalid coordinates.");
                        $updateWarnings[] = $warning;
                        Log::warning($warning, ['product_id' => $data->id]);
                        continue;
                    }
                    if ($rawX === null || $rawY === null) {
                        $rawX = 50;
                        $rawY = 50;
                    }
                }

                if ($target !== 'model3d') {
                    if ($rawX === null || $rawY === null) {
                        $warning = __("Hotspot skipped: missing coordinates.");
                        $updateWarnings[] = $warning;
                        Log::warning($warning, ['product_id' => $data->id]);
                        continue;
                    }
                    $x = $rawX / 100;
                    $y = $rawY / 100;
                    if ($x < 0 || $x > 1 || $y < 0 || $y > 1) {
                        $warning = __("Hotspot skipped: coordinates out of bounds.");
                        $updateWarnings[] = $warning;
                        Log::warning($warning, ['product_id' => $data->id]);
                        continue;
                    }
                } else {
                    $x = $rawX / 100;
                    $y = $rawY / 100;
                }
                $id = !empty($ids[$i]) ? (string) $ids[$i] : 'hs_' . substr(sha1(
                    sprintf('%.2f', $rawX) . '|' . sprintf('%.2f', $rawY) . '|' . $label . '|' . $description . '|' . $type
                ), 0, 12);
                $seenIds[] = $id;
                $existingImage = null;
                if (isset($existingItems[$id]['image'])) {
                    if (is_array($existingItems[$id]['image']) && !empty($existingItems[$id]['image']['src'])) {
                        $existingImage = $existingItems[$id]['image']['src'];
                    } elseif (is_string($existingItems[$id]['image'])) {
                        $existingImage = $existingItems[$id]['image'];
                    }
                }
                if ($existingImage && strpos($existingImage, '/assets/products_media/' . $data->id . '/hotspots/images/') === false) {
                    $updateWarnings[] = __("Hotspot image ignored (mismatched product).");
                    $existingImage = null;
                }
                $imageUrl = $existingImage;

                if ($request->hasFile('media_hotspot_image.' . $i)) {
                    $file = $request->file('media_hotspot_image.' . $i);
                    $ext = strtolower($file->getClientOriginalExtension());
                    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                        $updateWarnings[] = __("Hotspot image skipped (invalid type).");
                        $file = null;
                    }
                    if ($file && $file->getSize() > 2 * 1024 * 1024) {
                        $updateWarnings[] = __("Hotspot image skipped (max 2MB).");
                        $file = null;
                    }
                    if ($file && is_array($file)) {
                        $updateWarnings[] = __("Hotspot image skipped (multiple files).");
                        $file = null;
                    }

                    if ($file) {
                        $uploadDir = public_path('assets/products_media/' . $data->id . '/hotspots/images');
                        if (!file_exists($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }

                        if (!empty($existingImage)) {
                            $oldPath = parse_url($existingImage, PHP_URL_PATH);
                            if ($oldPath) {
                                $oldFile = public_path(ltrim($oldPath, '/'));
                                if (file_exists($oldFile)) {
                                    unlink($oldFile);
                                }
                            }
                        }

                        $fileName = 'hotspot_' . $id . '.' . $ext;
                        $file->move($uploadDir, $fileName);
                        $imageUrl = asset('assets/products_media/' . $data->id . '/hotspots/images/' . $fileName);
                    }
                } elseif (!empty($deleteFlags[$i])) {
                    if (!empty($existingImage)) {
                        $oldPath = parse_url($existingImage, PHP_URL_PATH);
                        if ($oldPath) {
                            $oldFile = public_path(ltrim($oldPath, '/'));
                            if (file_exists($oldFile)) {
                                unlink($oldFile);
                            }
                        }
                    }
                    $imageUrl = null;
                }

                $items[] = [
                    'id' => $id,
                    'type' => $type ?: 'text',
                    'label' => $label,
                    'description' => $description,
                    'image' => $imageUrl ? [
                        'src' => $imageUrl,
                        'width' => null,
                        'height' => null,
                    ] : null,
                    'position' => [
                        'x' => $x,
                        'y' => $y,
                    ],
                    'target' => $target ?: 'image',
                    'frame' => $frameValue,
                ];
            }

            if (!empty($existingItems)) {
                foreach ($existingItems as $oldId => $oldItem) {
                    $oldImage = null;
                    if (isset($oldItem['image'])) {
                        if (is_array($oldItem['image']) && !empty($oldItem['image']['src'])) {
                            $oldImage = $oldItem['image']['src'];
                        } elseif (is_string($oldItem['image'])) {
                            $oldImage = $oldItem['image'];
                        }
                    }
                    if (!in_array($oldId, $seenIds, true) && !empty($oldImage)) {
                        if (strpos($oldImage, '/assets/products_media/' . $data->id . '/hotspots/images/') === false) {
                            continue;
                        }
                        $oldPath = parse_url($oldImage, PHP_URL_PATH);
                        if ($oldPath) {
                            $oldFile = public_path(ltrim($oldPath, '/'));
                            if (file_exists($oldFile)) {
                                unlink($oldFile);
                            }
                        }
                    }
                }
            }

            $mediaExtra['hotspots'] = [
                'enabled' => $request->has('media_hotspot_enabled') ? 1 : 0,
                'target_image' => (string) $request->input('media_hotspot_base', ''),
                'items' => $items,
            ];
            $input['media_extra'] = json_encode($mediaExtra);
        }

        $hasModel3dUpdate = $request->has('media_3d_enabled') || $request->hasFile('media_3d_model') || array_key_exists('model3d', $mediaExtra);
        if ($hasModel3dUpdate) {
            $modelPath = null;
            if ($request->hasFile('media_3d_model')) {
                $file = $request->file('media_3d_model');
                $ext = strtolower($file->getClientOriginalExtension());
                if (!in_array($ext, ['glb', 'gltf'])) {
                    return response()->json(array('errors' => ['media_3d_model' => __("Only .glb or .gltf files are allowed.")]));
                }

                $uploadDir = public_path('assets/products_media/' . $data->id . '/3d');
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if (isset($mediaExtra['model3d']['src'])) {
                    $oldSrc = $mediaExtra['model3d']['src'];
                    $oldPath = parse_url($oldSrc, PHP_URL_PATH);
                    if ($oldPath) {
                        $oldFile = public_path(ltrim($oldPath, '/'));
                        if (file_exists($oldFile)) {
                            unlink($oldFile);
                        }
                    }
                }

                $fileName = time() . Str::random(8) . '.' . $ext;
                $file->move($uploadDir, $fileName);
                $modelPath = asset('assets/products_media/' . $data->id . '/3d/' . $fileName);
            } elseif (isset($mediaExtra['model3d']['src'])) {
                $modelPath = $mediaExtra['model3d']['src'];
            }

            $mediaExtra['model3d'] = [
                'enabled' => $request->has('media_3d_enabled') ? 1 : 0,
                'src' => $modelPath,
                'poster' => null,
                'viewer' => [
                    'auto_rotate' => (bool) $request->input('media_3d_auto_rotate', false),
                    'exposure' => $request->input('media_3d_exposure', null),
                    'camera_orbit' => $request->input('media_3d_camera_orbit', null),
                ],
            ];
            $input['media_extra'] = json_encode($mediaExtra);
        }

        $data->slug = Str::slug($data->name, '-') . '-' . strtolower($data->sku);

        $data->update($input);
        //-- Logic Section Ends

        //--- Redirect Section
        $msg = __("Product Updated Successfully.") . '<a href="' . route('admin-prod-index') . '">' . __("View Product Lists.") . '</a>';
        if ($hasV360Update && $request->has('media_360_enabled') && $frameCount > 0 && $frameCount < 8) {
            $updateWarnings[] = __("Warning: 360° view needs at least 8 frames.");
        }
        if (!empty($updateWarnings)) {
            foreach ($updateWarnings as $warning) {
                $msg .= '<br>' . $warning;
            }
        }
        return response()->json($msg);
        //--- Redirect Section Ends
    }

    //*** GET Request
    public function feature($id)
    {
        $data = Product::findOrFail($id);
        return view('admin.product.highlight', compact('data'));
    }

    //*** POST Request
    public function featuresubmit(Request $request, $id)
    {
        //-- Logic Section
        $data = Product::findOrFail($id);
        $input = $request->all();
        if ($request->featured == "") {
            $input['featured'] = 0;
        }
        if ($request->hot == "") {
            $input['hot'] = 0;
        }
        if ($request->best == "") {
            $input['best'] = 0;
        }
        if ($request->top == "") {
            $input['top'] = 0;
        }
        if ($request->latest == "") {
            $input['latest'] = 0;
        }
        if ($request->big == "") {
            $input['big'] = 0;
        }
        if ($request->trending == "") {
            $input['trending'] = 0;
        }
        if ($request->sale == "") {
            $input['sale'] = 0;
        }
        if ($request->is_discount == "") {
            $input['is_discount'] = 0;
            $input['discount_date'] = null;
        } else {
            $input['discount_date'] = \Carbon\Carbon::parse($input['discount_date'])->format('Y-m-d');
        }

        $data->update($input);
        //-- Logic Section Ends

        //--- Redirect Section
        $msg = __('Highlight Updated Successfully.');
        return response()->json($msg);
        //--- Redirect Section Ends

    }

    //*** GET Request
    public function destroy($id)
    {

        $data = Product::findOrFail($id);
        if ($data->galleries->count() > 0) {
            foreach ($data->galleries as $gal) {
                if (file_exists(public_path() . '/assets/images/galleries/' . $gal->photo)) {
                    unlink(public_path() . '/assets/images/galleries/' . $gal->photo);
                }
                $gal->delete();
            }

        }

        if ($data->reports->count() > 0) {
            foreach ($data->reports as $gal) {
                $gal->delete();
            }
        }

        if ($data->ratings->count() > 0) {
            foreach ($data->ratings as $gal) {
                $gal->delete();
            }
        }
        if ($data->wishlists->count() > 0) {
            foreach ($data->wishlists as $gal) {
                $gal->delete();
            }
        }
        if ($data->clicks->count() > 0) {
            foreach ($data->clicks as $gal) {
                $gal->delete();
            }
        }
        if ($data->comments->count() > 0) {
            foreach ($data->comments as $gal) {
                if ($gal->replies->count() > 0) {
                    foreach ($gal->replies as $key) {
                        $key->delete();
                    }
                }
                $gal->delete();
            }
        }

        if (!filter_var($data->photo, FILTER_VALIDATE_URL)) {
            if ($data->photo) {
                if (file_exists(public_path() . '/assets/images/products/' . $data->photo)) {
                    unlink(public_path() . '/assets/images/products/' . $data->photo);
                }
            }

        }

        if (file_exists(public_path() . '/assets/images/thumbnails/' . $data->thumbnail) && $data->thumbnail != "") {
            unlink(public_path() . '/assets/images/thumbnails/' . $data->thumbnail);
        }

        if ($data->file != null) {
            if (file_exists(public_path() . '/assets/files/' . $data->file)) {
                unlink(public_path() . '/assets/files/' . $data->file);
            }
        }
        $data->delete();
        //--- Redirect Section
        $msg = __('Product Deleted Successfully.');
        return response()->json($msg);
        //--- Redirect Section Ends

// PRODUCT DELETE ENDS
    }

    public function catalog($id1, $id2)
    {
        $data = Product::findOrFail($id1);
        $data->is_catalog = $id2;
        $data->update();
        if ($id2 == 1) {
            $msg = "Product added to catalog successfully.";
        } else {
            $msg = "Product removed from catalog successfully.";
        }
        return response()->json($msg);
    }

    public function settingUpdate(Request $request)
    {
        //--- Logic Section
        $input = $request->all();
        $data = \App\Models\Generalsetting::findOrFail(1);

        if (!empty($request->product_page)) {
            $input['product_page'] = implode(',', $request->product_page);
        } else {
            $input['product_page'] = null;
        }

        if (!empty($request->wishlist_page)) {
            $input['wishlist_page'] = implode(',', $request->wishlist_page);
        } else {
            $input['wishlist_page'] = null;
        }

        cache()->forget('generalsettings');

        $data->update($input);
        //--- Logic Section Ends

        //--- Redirect Section
        $msg = __('Data Updated Successfully.');
        return response()->json($msg);
        //--- Redirect Section Ends
    }

    public function getAttributes(Request $request)
    {
        $model = '';
        if ($request->type == 'category') {
            $model = 'App\Models\Category';
        } elseif ($request->type == 'subcategory') {
            $model = 'App\Models\Subcategory';
        } elseif ($request->type == 'childcategory') {
            $model = 'App\Models\Childcategory';
        }

        $attributes = Attribute::where('attributable_id', $request->id)->where('attributable_type', $model)->get();
        $attrOptions = [];
        foreach ($attributes as $key => $attribute) {
            $options = AttributeOption::where('attribute_id', $attribute->id)->get();
            $attrOptions[] = ['attribute' => $attribute, 'options' => $options];
        }
        return response()->json($attrOptions);
    }

    public function getCrossProduct($catId)
    {
        $crossProducts = Product::where('category_id', $catId)->where('status', 1)->get();
        return view('load.cross_product', compact('crossProducts'));
    }

}
