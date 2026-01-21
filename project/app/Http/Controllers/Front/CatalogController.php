<?php

namespace App\Http\Controllers\Front;

use App\Models\Category;
use App\Models\Childcategory;
use App\Models\CategorySlider;
use App\Models\FeaturedBanner;
use App\Models\Partner;
use App\Models\Product;
use App\Models\Report;
use App\Models\Seotool;
use App\Models\Slider;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class CatalogController extends FrontBaseController
{

    // CATEGORIES SECTOPN

    public function categories()
    {
        $categories = Category::where('status', 1)->get();
        return view('frontend.products', compact('categories'));
    }

    // -------------------------------- CATEGORY SECTION ----------------------------------------

    public function category(Request $request, $slug = null, $slug1 = null, $slug2 = null, $slug3 = null)
    {
       
        $data['categories'] = Category::where('status', 1)->get();

        /**
         * Theme selection (single source of truth)
         * - Default for category route: theme4
         * - Theme 4 is the production category experience (default; no query param required)
         * - theme1/theme2/theme3 are preview-only for backward compatibility via ?theme=
         * - ?theme=theme4 is treated as default (ignored)
         */
        $defaultTheme = 'theme4';
        $requestedTheme = $request->query('theme');
        $previewThemes = ['theme1', 'theme2', 'theme3'];
        $activeTheme = in_array($requestedTheme, $previewThemes, true) ? $requestedTheme : $defaultTheme;
        $data['activeTheme'] = $activeTheme;

        // Hard-lock full-page category rendering to Theme 4.
        // Legacy category views are allowed ONLY for AJAX requests (e.g., partial updates).
        if (!$request->ajax()) {
            $activeTheme = 'theme4';
            $data['activeTheme'] = $activeTheme;
        }

        if ($request->view_check) {
            session::put('view', $request->view_check);
        }

        //   dd(session::get('view'));

        $cat = null;
        $subcat = null;
        $childcat = null;
        $flash = null;
        $minprice = $request->min;
        $maxprice = $request->max;
        $sort = $request->sort;
        $search = $request->search;
        $pageby = $request->pageby;

        $minprice = ($minprice / $this->curr->value);
        $maxprice = ($maxprice / $this->curr->value);
        $type = $request->has('type') ?? '';

        if (!empty($slug)) {
            $cat = Category::where('slug', $slug)->firstOrFail();
            $data['cat'] = $cat;
        }

        if (!empty($slug1)) {
            $subcat = Subcategory::where('slug', $slug1)->firstOrFail();
            $data['subcat'] = $subcat;
        }

        // Validate URL hierarchy: category -> subcategory
        if (!empty($subcat) && !empty($cat) && (int) $subcat->category_id !== (int) $cat->id) {
            abort(404);
        }
        if (!empty($slug2)) {
            $childcat = Childcategory::where('slug', $slug2)->firstOrFail();
            $data['childcat'] = $childcat;
        }

        // Validate URL hierarchy: subcategory -> childcategory
        if (!empty($childcat) && !empty($subcat) && (int) $childcat->subcategory_id !== (int) $subcat->id) {
            abort(404);
        }

        $data['filterState'] = [
            'sort' => $sort,
            'pageby' => $pageby,
            'min' => $request->query('min'),
            'max' => $request->query('max'),
            'rating_min' => $request->query('rating_min'),
            'availability' => (array) $request->query('availability', []),
            'search' => $search,
        ];

        $attributeCollection = collect();
        if (!empty($cat)) {
            $attributeCollection = $attributeCollection->merge(
                $cat->attributes()->with('attribute_options')->get()
            );
        }
        if (!empty($subcat)) {
            $attributeCollection = $attributeCollection->merge(
                $subcat->attributes()->with('attribute_options')->get()
            );
        }
        if (!empty($childcat)) {
            $attributeCollection = $attributeCollection->merge(
                $childcat->attributes()->with('attribute_options')->get()
            );
        }
        $data['filterAttributes'] = $attributeCollection->unique('id')->values();

        $data['categoryHeroImage'] = null;
        if (!empty($cat) && !empty($cat->image)) {
            $data['categoryHeroImage'] = asset('assets/images/categories/' . $cat->image);
        }

        $data['exploreTiles'] = collect();
        if (!empty($cat)) {
            $subcategories = $cat->subs()
                ->with(['products' => function ($query) {
                    $query->where('status', 1)
                        ->select('id', 'subcategory_id', 'slug', 'name', 'thumbnail')
                        ->latest('id');
                }])
                ->get();

            $subcategoriesTable = (new \App\Models\Subcategory())->getTable();
            $subImageField = null;
            if (Schema::hasTable($subcategoriesTable)) {
                if (Schema::hasColumn($subcategoriesTable, 'image')) {
                    $subImageField = 'image';
                } elseif (Schema::hasColumn($subcategoriesTable, 'photo')) {
                    $subImageField = 'photo';
                }
            }

            // Prefer subcategories that have an image field value.
            if ($subImageField) {
                $withImage = $subcategories->filter(function ($subcategory) use ($subImageField) {
                    return !empty($subcategory->{$subImageField});
                });
                $withoutImage = $subcategories->reject(function ($subcategory) use ($subImageField) {
                    return !empty($subcategory->{$subImageField});
                });
                $subcategories = $withImage->concat($withoutImage);
            }

            $data['exploreTiles'] = $subcategories->map(function ($subcategory) use ($cat, $subImageField) {
                $image = null;

                if ($subImageField && !empty($subcategory->{$subImageField})) {
                    // If your project stores subcategory images elsewhere, update this path.
                    $image = asset('assets/images/subcategories/' . $subcategory->{$subImageField});
                }

                if (!$image) {
                    $product = $subcategory->products->first();
                    $image = $product && $product->thumbnail
                        ? asset('assets/images/thumbnails/' . $product->thumbnail)
                        : asset('assets/images/noimage.png');
                }

                return [
                    'label' => $subcategory->name,
                    'image' => $image,
                    'url' => route('front.category', [$cat->slug, $subcategory->slug]),
                    'slug' => $subcategory->slug,
                    'count' => $subcategory->products ? $subcategory->products->count() : 0,
                ];
            })->take(8)->values();
        }

        $data['partners'] = Schema::hasTable((new Partner())->getTable())
            ? Partner::all()
            : collect();

        $data['categorySliders'] = CategorySlider::where('status', 1)
            ->where(function ($q) use ($cat) {
                if ($cat) {
                    $q->where('category_id', $cat->id);
                }
                $q->orWhereNull('category_id');
            })
            ->orderBy('sort_order')
            ->get();

        $data['featuredBanners'] = Schema::hasTable((new FeaturedBanner())->getTable())
            ? FeaturedBanner::latest('id')->get()
            : collect();

        $pageNode = $childcat ?? $subcat ?? $cat;
        $data['pageTitle'] = $pageNode ? $pageNode->name : $this->gs->title;
        $data['pageMetaDescription'] = $this->gs->title;
        if (Schema::hasTable((new Seotool())->getTable())) {
            $data['pageMetaDescription'] = optional(Seotool::find(1))->meta_description ?? $this->gs->title;
        }

        $data['latest_products'] = Product::with('user')->whereStatus(1)->whereLatest(1)
            ->whereHas('user', function ($q) {
                $q->where('is_vendor', 2);
            })
            ->when('user', function ($query) {
                foreach ($query as $q) {
                    if ($q->is_vendor == 2) {
                        return $q;
                    }
                }
            })
            ->withCount('ratings')
            ->withAvg('ratings', 'rating')
            ->take(5)
            ->get();

        /**
         * Theme 4 only:
         * - Avoid get()->paginate() memory usage by paginating at DB level
         * - Provide isolated queries for sections (best sellers / top recommendations / featured)
         */
        if (!$request->ajax() && $activeTheme === 'theme4') {
            $ratingMin = $request->query('rating_min');
            $availability = (array) $request->query('availability', []);

            $applyTheme4Filters = function ($query) use ($ratingMin, $availability) {
                // Rating filter: ratings_avg_rating >= selected value (requires withAvg alias)
                if (!empty($ratingMin) && is_numeric($ratingMin)) {
                    // MySQL allows HAVING without GROUP BY; alias is produced by withAvg(...)
                    $query->havingRaw('ratings_avg_rating >= ?', [(float) $ratingMin]);
                }

                // Availability filter: stackable
                $hasInStock = in_array('in_stock', $availability, true);
                $hasOutOfStock = in_array('out_of_stock', $availability, true);
                if ($hasInStock && !$hasOutOfStock) {
                    $query->where('stock', '>', 0);
                } elseif ($hasOutOfStock && !$hasInStock) {
                    $query->where('stock', '=', 0);
                }

                return $query;
            };

            $makeBaseQuery = function () use ($applyTheme4Filters, $cat, $subcat, $childcat, $type, $search, $minprice, $maxprice, $request) {
                $query = Product::with('user')
                    ->when($cat, function ($q) use ($cat) {
                        return $q->where('category_id', $cat->id);
                    })
                    ->when($subcat, function ($q) use ($subcat) {
                        return $q->where('subcategory_id', $subcat->id);
                    })
                    ->when($childcat, function ($q) use ($childcat) {
                        return $q->where('childcategory_id', $childcat->id);
                    })
                    ->when($type, function ($q) {
                        return $q->whereStatus(1)->whereIsDiscount(1)
                            ->where('discount_date', '>=', date('Y-m-d'));
                    })
                    ->when($search, function ($q) use ($search) {
                        return $q->where(function ($sq) use ($search) {
                            $sq->where('name', 'like', '%' . $search . '%')
                                ->orWhere('name', 'like', $search . '%');
                        });
                    })
                    ->when($minprice, function ($q) use ($minprice) {
                        return $q->where('price', '>=', $minprice);
                    })
                    ->when($maxprice, function ($q) use ($maxprice) {
                        return $q->where('price', '<=', $maxprice);
                    })
                    ->where('status', 1)
                    ->withCount('ratings')
                    ->withAvg('ratings', 'rating');

                // Dynamic attribute filters (same logic as legacy)
                $query->where(function ($q) use ($cat, $subcat, $childcat, $request) {
                    if (!empty($cat)) {
                        foreach ($cat->attributes()->get() as $attribute) {
                            $inname = $attribute->input_name;
                            $chFilters = $request["$inname"];
                            if (!empty($chFilters)) {
                                $q->where(function ($subQ) use ($chFilters) {
                                    foreach ($chFilters as $idx => $chFilter) {
                                        if ($idx === 0) {
                                            $subQ->where('attributes', 'like', '%' . '"' . $chFilter . '"' . '%');
                                        } else {
                                            $subQ->orWhere('attributes', 'like', '%' . '"' . $chFilter . '"' . '%');
                                        }
                                    }
                                });
                            }
                        }
                    }

                    if (!empty($subcat)) {
                        foreach ($subcat->attributes()->get() as $attribute) {
                            $inname = $attribute->input_name;
                            $chFilters = $request["$inname"];
                            if (!empty($chFilters)) {
                                $q->where(function ($subQ) use ($chFilters) {
                                    foreach ($chFilters as $idx => $chFilter) {
                                        if ($idx === 0) {
                                            $subQ->where('attributes', 'like', '%' . '"' . $chFilter . '"' . '%');
                                        } else {
                                            $subQ->orWhere('attributes', 'like', '%' . '"' . $chFilter . '"' . '%');
                                        }
                                    }
                                });
                            }
                        }
                    }

                    if (!empty($childcat)) {
                        foreach ($childcat->attributes()->get() as $attribute) {
                            $inname = $attribute->input_name;
                            $chFilters = $request["$inname"];
                            if (!empty($chFilters)) {
                                $q->where(function ($subQ) use ($chFilters) {
                                    foreach ($chFilters as $idx => $chFilter) {
                                        if ($idx === 0) {
                                            $subQ->where('attributes', 'like', '%' . '"' . $chFilter . '"' . '%');
                                        } else {
                                            $subQ->orWhere('attributes', 'like', '%' . '"' . $chFilter . '"' . '%');
                                        }
                                    }
                                });
                            }
                        }
                    }
                });

                return $applyTheme4Filters($query);
            };

            // 1) Main listing (DB-level paginate)
            $mainQuery = $makeBaseQuery();
            if ($sort === 'date_desc') {
                $mainQuery->latest('id');
            } elseif ($sort === 'date_asc') {
                $mainQuery->oldest('id');
            } elseif ($sort === 'price_desc') {
                $mainQuery->orderByDesc('price');
            } elseif ($sort === 'price_asc') {
                $mainQuery->orderBy('price');
            } else {
                $mainQuery->latest('id');
            }

            $perPage = isset($pageby) ? (int) $pageby : (int) $this->gs->page_count;
            $perPage = $perPage > 0 ? $perPage : (int) $this->gs->page_count;

            $prodsPaginator = $mainQuery->paginate($perPage)->withQueryString();
            // Keep vendor/attribute derived price logic but only for the current page
            $prodsPaginator->setCollection(
                $prodsPaginator->getCollection()->map(function ($item) {
                    $item->price = $item->vendorSizePrice();
                    return $item;
                })
            );
            $data['prods'] = $prodsPaginator;

            // 2) Section queries
            $take = 8;

            // Best Sellers: use sales_count if it exists, else fall back to views
            $bestSellerOrderColumn = \Illuminate\Support\Facades\Schema::hasColumn('products', 'sales_count')
                ? 'sales_count'
                : 'views';

            $bestSellers = $makeBaseQuery()
                ->orderByDesc($bestSellerOrderColumn)
                ->take($take)
                ->get()
                ->map(function ($item) {
                    $item->price = $item->vendorSizePrice();
                    return $item;
                });

            $topRecommendations = $makeBaseQuery()
                ->orderByDesc('ratings_avg_rating')
                ->take($take)
                ->get()
                ->map(function ($item) {
                    $item->price = $item->vendorSizePrice();
                    return $item;
                });

            $featuredProducts = $makeBaseQuery()
                ->where('featured', 1)
                ->latest('id')
                ->take($take)
                ->get()
                ->map(function ($item) {
                    $item->price = $item->vendorSizePrice();
                    return $item;
                });

            $data['t4BestSellers'] = $bestSellers;
            $data['t4TopRecommendations'] = $topRecommendations;
            $data['t4FeaturedProducts'] = $featuredProducts;

            // Category-page sections (backend-driven titles; Blade should not hardcode section titles)
            $resolveSectionTitle = function (?Category $category, array $candidateFields, string $defaultTitle) {
                if (empty($category)) {
                    return $defaultTitle;
                }
                foreach ($candidateFields as $field) {
                    $value = $category->getAttribute($field);
                    if (is_string($value) && trim($value) !== '') {
                        return trim($value);
                    }
                }
                return $defaultTitle;
            };

            $data['categorySections'] = [
                [
                    'key' => 'featured',
                    'title' => $resolveSectionTitle($cat ?? null, [
                        'featured_section_title',
                        'section_title_featured',
                        't4_featured_title',
                    ], 'Featured Products'),
                    'products' => $featuredProducts,
                ],
                [
                    'key' => 'best',
                    'title' => $resolveSectionTitle($cat ?? null, [
                        'best_section_title',
                        'best_sellers_section_title',
                        'section_title_best',
                        't4_best_title',
                    ], 'Best Sellers'),
                    'products' => $bestSellers,
                ],
                [
                    'key' => 'top',
                    'title' => $resolveSectionTitle($cat ?? null, [
                        'top_section_title',
                        'top_rated_section_title',
                        'section_title_top',
                        't4_top_title',
                    ], 'Top Rated'),
                    'products' => $topRecommendations,
                ],
            ];

            // Standardize sections for the view (title + products)
            $data['t4Sections'] = [
                ['key' => 'best_sellers', 'title' => 'Best Sellers', 'products' => $bestSellers],
                ['key' => 'top_recommendations', 'title' => 'Top recommendation', 'products' => $topRecommendations],
                ['key' => 'featured', 'title' => 'Featured', 'products' => $featuredProducts],
            ];

            // Theme4 category page is never rendered via legacy AJAX partial
            return view('frontend.theme4.category.index', $data);
        }

        $prods = Product::with('user')->when($cat, function ($query, $cat) {
            return $query->where('category_id', $cat->id);
        })
            ->when($subcat, function ($query, $subcat) {
                return $query->where('subcategory_id', $subcat->id);
            })
            ->when($type, function ($query, $type) {
                return $query->with('user')->whereStatus(1)->whereIsDiscount(1)
                    ->where('discount_date', '>=', date('Y-m-d'))
                    ->whereHas('user', function ($user) {
                        $user->where('is_vendor', 2);
                    });
            })
            ->when($childcat, function ($query, $childcat) {
                return $query->where('childcategory_id', $childcat->id);
            })
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%')->orWhere('name', 'like', $search . '%');
            })
            ->when($minprice, function ($query, $minprice) {
                return $query->where('price', '>=', $minprice);
            })
            ->when($maxprice, function ($query, $maxprice) {
                return $query->where('price', '<=', $maxprice);
            })
            ->when($sort, function ($query, $sort) {
                if ($sort == 'date_desc') {
                    return $query->latest('id');
                } elseif ($sort == 'date_asc') {
                    return $query->oldest('id');
                } elseif ($sort == 'price_desc') {
                    return $query->latest('price');
                } elseif ($sort == 'price_asc') {
                    return $query->oldest('price');
                }
            })
            ->when(empty($sort), function ($query, $sort) {
                return $query->latest('id');
            })
            ->withCount('ratings')
            ->withAvg('ratings', 'rating');

        $prods = $prods->where(function ($query) use ($cat, $subcat, $childcat, $type, $request) {
            $flag = 0;
            if (!empty($cat)) {
                foreach ($cat->attributes()->get() as $key => $attribute) {
                    $inname = $attribute->input_name;
                    $chFilters = $request["$inname"];

                    if (!empty($chFilters)) {
                        $flag = 1;
                        foreach ($chFilters as $key => $chFilter) {
                            if ($key == 0) {
                                $query->where('attributes', 'like', '%' . '"' . $chFilter . '"' . '%');
                            } else {
                                $query->orWhere('attributes', 'like', '%' . '"' . $chFilter . '"' . '%');
                            }
                        }
                    }
                }
            }

            if (!empty($subcat)) {
                foreach ($subcat->attributes()->get() as $attribute) {
                    $inname = $attribute->input_name;
                    $chFilters = $request["$inname"];

                    if (!empty($chFilters)) {
                        $flag = 1;
                        foreach ($chFilters as $key => $chFilter) {
                            if ($key == 0 && $flag == 0) {
                                $query->where('attributes', 'like', '%' . '"' . $chFilter . '"' . '%');
                            } else {
                                $query->orWhere('attributes', 'like', '%' . '"' . $chFilter . '"' . '%');
                            }
                        }
                    }
                }
            }

            if (!empty($childcat)) {
                foreach ($childcat->attributes()->get() as $attribute) {
                    $inname = $attribute->input_name;
                    $chFilters = $request["$inname"];

                    if (!empty($chFilters)) {
                        $flag = 1;
                        foreach ($chFilters as $key => $chFilter) {
                            if ($key == 0 && $flag == 0) {
                                $query->where('attributes', 'like', '%' . '"' . $chFilter . '"' . '%');
                            } else {
                                $query->orWhere('attributes', 'like', '%' . '"' . $chFilter . '"' . '%');
                            }
                        }
                    }
                }
            }
        });

        $prods = $prods->where('status', 1)->get()

            ->map(function ($item) {
                $item->price = $item->vendorSizePrice();
                return $item;
            })->paginate(isset($pageby) ? $pageby : $this->gs->page_count);
        $data['prods'] = $prods;

        // Backward compatibility: keep preview query params (including ?theme=theme1|2|3) across pagination links.
        if (method_exists($prods, 'appends')) {
            $prods->appends($request->query());
        }

        if ($request->ajax()) {
            $data['ajax_check'] = 1;
            return view('frontend.ajax.category', $data);
        }

        return view('frontend.products', $data);
    }

    public function getsubs(Request $request)
    {
        $category = Category::where('slug', $request->category)->firstOrFail();
        $subcategories = Subcategory::where('category_id', $category->id)->get();
        return $subcategories;
    }
    public function report(Request $request)
    {

        //--- Validation Section
        $rules = [
            'note' => 'max:400',
        ];
        $customs = [
            'note.max' => 'Note Must Be Less Than 400 Characters.',
        ];
        
        $request->validate($rules, $customs);


        $data = new Report;
        $input = $request->all();
        $data->fill($input)->save();
        return back()->with('success', 'Report has been sent successfully.');

    }
}
