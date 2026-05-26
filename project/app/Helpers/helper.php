<?php

function wishlistCheck($product_id)
{
    $wishlist = \App\Models\Wishlist::where('product_id', $product_id)->where('user_id', auth()->id())->first();
    if ($wishlist) {
        return true;
    } else {
        return false;
    }

}

function addon($name)
{

    if ($name == "otp") {
        $otp = file_exists(base_path("/vendor/markury/src/Adapter/addon/otp.txt"));
        if ($otp) {
            $data = file_get_contents(base_path("/vendor/markury/src/Adapter/addon/otp.txt"));

            if ($data) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    return false;
}

function versioned_asset($path)
{
    $fullPath = public_path($path);
    if (file_exists($fullPath)) {
        $version = filemtime($fullPath);
        return asset($path) . '?v=' . $version;
    }
    return asset($path);
}

/**
 * Return asset URL via CDN if configured, otherwise Laravel's asset().
 *
 * @param string $path
 * @return string
 */
function cdn_asset($path)
{
    $cdn = env('CDN_URL');
    if ($cdn) {
        $p = ltrim($path, '/');
        return rtrim($cdn, '/') . '/' . $p;
    }

    return asset($path);
}
