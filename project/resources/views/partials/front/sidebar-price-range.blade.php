                        <!-- Price Range -->
                        <div class="single-product-widget">
                            <h5 class="widget-title">@lang('Price Range')</h5>
                            <div class="price-range">
                                <div class="d-none">
                                    <!-- start value -->
                                    <input id="start_value" type="number" name="min"
                                        value="{{ isset($_GET['min']) ? $_GET['min'] : $gs->min_price }}">
                                    <!-- end value -->
                                    <input id="end_value" type="number"
                                        value="{{ isset($_GET['max']) ? $_GET['max'] : $gs->max_price }}">
                                    <!-- max value -->
                                    <input id="max_value" type="number" name="max" value="{{ $gs->max_price }}">
                                </div>
                                <div id="slider-range"></div>

                                <input type="text" id="amount" readonly class="range_output">
                            </div>

                            <button class="template-btn mt-3 w-100" id="price_filter">@lang('Apply Filter')</button>
                            <a href="{{ route('front.category') }}"
                                class="template-btn dark-btn w-100 mt-3">@lang('Clear Filter')</a>
                        </div>
