(function ($) {
  "use strict";

  function commerceState() {
    return window.Commerce && typeof window.Commerce.state === "function"
      ? window.Commerce.state()
      : null;
  }

  function commerceCan(capability) {
    if (!window.Commerce || typeof window.Commerce.can !== "function") return true;
    return !!window.Commerce.can(capability);
  }

  function commerceWhatsAppMessage(actionLabel) {
    if (window.Commerce && typeof window.Commerce.buildWhatsAppMessage === "function") {
      return window.Commerce.buildWhatsAppMessage(actionLabel);
    }
    var lines = [];
    if (actionLabel) lines.push(String(actionLabel));

    // Best-effort: mirrors the same selectors used for add-to-cart payload.
    var pid = $("#product_id").val();
    var qty = $("#order-qty").val();
    var size = $(".cart_size input:checked").attr("data-key") || $(".cart_size input:checked").val();
    var color = $(".cart_color input:checked").attr("data-color") || $(".cart_color input:checked").val();

    if (pid) lines.push("Product ID: " + pid);
    if (qty) lines.push("Qty: " + qty);
    if (size) lines.push("Size: " + size);
    if (color) lines.push("Color: " + color);

    var attrs = $(".cart_attr:checked")
      .map(function () {
        var k = $(this).attr("data-key");
        var v = $(this).val();
        return k && v ? k + ": " + v : null;
      })
      .get()
      .filter(Boolean);
    if (attrs && attrs.length) lines = lines.concat(attrs);

    lines.push(document.title);
    lines.push(window.location.href);
    return lines.filter(Boolean).join("\n");
  }

  function commerceBlocked(actionLabel) {
    var st = commerceState();
    var mode = st && st.mode ? st.mode : "enabled";
    var msg =
      mode === "disabled"
        ? "Store is temporarily unavailable."
        : mode === "whatsapp_only"
          ? "Ordering is available via WhatsApp."
          : "This feature is temporarily unavailable.";

    // WhatsApp-only: open WhatsApp chat (best-effort) instead of failing silently.
    if (mode === "whatsapp_only" && window.Commerce && typeof window.Commerce.whatsappLink === "function") {
      var wa = window.Commerce.whatsappLink(commerceWhatsAppMessage(actionLabel || "Order request"));
      if (wa) {
        window.open(wa, "_blank", "noopener");
        return true;
      }
    }

    if (window.toastr) {
      toastr.error(msg);
    } else {
      alert(msg);
    }
    return true;
  }

  /**
   * PERFORMANCE FIX: Add timeout & error handlers to all AJAX calls
   * Prevents infinite loaders when requests fail or timeout
   */
  $.ajaxSetup({
    timeout: 5000, // 5 second global timeout to prevent hanging requests
    error: function () {
      $(".gocover").hide(); // Hide loader on any AJAX error
    },
    complete: function () {
      // Hide any loaders in complete handler (fires after success OR error)
      setTimeout(function () {
        if ($(".gocover").is(":visible") && !$("body").hasClass("processing")) {
          $(".gocover").hide();
        }
      }, 100);
    }
  });

  //   wishlist
  $(document).on("click", ".wishlist", function (e) {
    e.preventDefault();
    const $this = $(this);
    if ($(this).data("href")) {
      $.ajax({
        url: $(this).data("href"),
        type: 'GET',
        timeout: 5000,
        success: function (data) {
          if (data[0] == 1) {
            toastr.success(data["success"]);
            $("#wishlist-count").html(data[1]);
            $this.children().addClass("active");
          } else {
            toastr.error(data["error"]);
          }
        },
        error: function (xhr, status, error) {
          console.error("Wishlist error:", error);
          toastr.error("Failed to update wishlist. Please try again.");
        },
        complete: function () {
          $(".gocover").hide();
        }
      });
    }
  });

  $(document).on("click", ".removewishlist", function (e) {
    e.preventDefault();
    let $this = $(this);
    $.ajax({
      url: $(this).attr("data-href"),
      type: 'GET',
      timeout: 5000,
      success: function (data) {
        $("#wishlist-count").html(data[1]);
        $this.parent().parent().parent().remove();
      },
      error: function (xhr, status, error) {
        console.error("Remove wishlist error:", error);
        toastr.error("Failed to remove from wishlist. Please try again.");
      },
      complete: function () {
        $(".gocover").hide();
      }
    });
  });

  //   compare
  $(document).on("click", ".compare_product", function (e) {
    e.preventDefault();
    $.ajax({
      url: $(this).data("href"),
      type: 'GET',
      timeout: 5000,
      success: function (data) {
        $("#compare-count").html(data[1]);
        $("#compare-count1").html(data[1]);
        if (data[0] == 0) {
          toastr.success(data["success"]);
        } else {
          toastr.error(data["error"]);
        }
      },
      error: function (xhr, status, error) {
        console.error("Compare error:", error);
        toastr.error("Failed to update compare. Please try again.");
      },
      complete: function () {
        $(".gocover").hide();
      }
    });
  });

  // Product Add Qty
  $(document).on("click", ".qtplus", function () {
    var $tselector = $("#order-qty");
    var stock = $("#stock").val();
    var total = $($tselector).val();
    if (stock != "") {
      var stk = parseInt(stock);
      if (total < stk) {
        total++;
        $($tselector).val(total);
      }
    } else {
      total++;
    }

    $($tselector).val(total);
  });

  // Product Minus Qty
  $(document).on("click", ".qtminus", function () {
    var $tselector = $("#order-qty");
    var total = $($tselector).val();
    if (total > 1) {
      total--;
    }
    $($tselector).val(total);
  });

  $(".qttotal").keypress(function (e) {
    if (this.value.length == 0 && e.which == 48) {
      return false;
    }
    if (e.which != 8 && e.which != 32) {
      if (isNaN(String.fromCharCode(e.which))) {
        e.preventDefault();
      }
    }
  });

  // aDD TO FAVORITE
  $(document).on("click", ".favorite-prod", function () {
    var $this = $(this);
    $.ajax({
      url: $(this).data("href"),
      type: 'GET',
      timeout: 5000,
      success: function (data) {
        $this.attr("data-href", "");
        $this.attr("disabled", true);
        $this.removeClass("favorite-prod");
        $this.html(data["icon"] + " " + data["text"]);
      },
      error: function (xhr, status, error) {
        console.error("Favorite error:", error);
        toastr.error("Failed to update favorite. Please try again.");
      },
      complete: function () {
        $(".gocover").hide();
      }
    });
  });

  $(document).on("click", ".stars", function () {
    $(".stars").removeClass("active");
    $(this).addClass("active");
    $("#rating").val($(this).data("val"));
  });

  // add to card
  $(document).on("click", ".add_cart_click", function (e) {
    if (!commerceCan("cart")) {
      e.preventDefault();
      commerceBlocked("Add to cart");
      return false;
    }
    e.preventDefault();
    
    $(".gocover").show(); // Show loader at start
    
    $.ajax({
      url: $(this).attr("data-href"),
      type: 'GET',
      timeout: 5000,
      success: function (data) {
        $(".gocover").hide();
        if (data == "digital") {
          toastr.error(lang.cart_already);
        } else if (data[0] == 0) {
          toastr.error(lang.cart_out);
        } else {
          $("#cart-count").html(data[0]);
          $("#cart-count1").html(data[0]);
          $("#total-cost").html(data[1]);
          $(".cart-popup").load(mainurl + "/carts/view");
          toastr.success(lang.cart_success);
        }
      },
      error: function (xhr, status, error) {
        $(".gocover").hide();
        console.error("Add to cart error:", error);
        if (status === "timeout") {
          toastr.error("Request timed out. Please try again.");
        } else {
          toastr.error("Failed to add item to cart. Please try again.");
        }
      },
      complete: function () {
        $(".gocover").hide();
      }
    });
    return false;
  });

  $(document).on("click", ".quantity-up", function () {
    if (!commerceCan("cart")) {
      commerceBlocked("Update cart quantity");
      return false;
    }
    var pid = $(this).parent().find(".prodid").val();
    var itemid = $(this).parent().find(".itemid").val();
    var size_qty = $(this).parent().find(".size_qty").val();

    var size_price = $(this)
      .parent()
      .parent()
      .parent()
      .find(".size_price")
      .val();

    var stck = $("#stock" + itemid).val();
    var qty = parseInt($("#qty" + itemid).val());
    if (stck != "") {
      var stk = parseInt(stck);
      if (qty < stk) {
        qty++;
        $("#qty" + itemid).html(qty);
      }
    } else {
      qty++;
      $("#qty" + itemid).html(qty);
    }
    
    $(".gocover").show(); // Show loader at start
    
    $.ajax({
      type: "GET",
      url: mainurl + "/addbyone",
      timeout: 5000,
      data: {
        id: pid,
        itemid: itemid,
        size_qty: size_qty,
        size_price: size_price,
      },
      success: function (data) {
        $(".gocover").hide();
        if (data == 0) {
          toastr.error(lang.cart_out);
        } else {
          $.ajax({
            url: mainurl + "/carts",
            type: 'GET',
            timeout: 5000,
            success: function (response) {
              $(".load_cart").html(response);
            },
            error: function () {
              toastr.error("Failed to update cart. Please refresh the page.");
            },
            complete: function () {
              $(".gocover").hide();
            }
          });
        }
      },
      error: function (xhr, status, error) {
        $(".gocover").hide();
        console.error("Quantity update error:", error);
        toastr.error("Failed to update quantity. Please try again.");
      },
      complete: function () {
        $(".gocover").hide();
      }
    });
  });

  $(document).on("click", ".quantity-down", function () {
    if (!commerceCan("cart")) {
      commerceBlocked("Update cart quantity");
      return false;
    }
    var pid = $(this).siblings(".prodid").val();
    var itemid = $(this).siblings(".itemid").val();
    var size_qty = $(this).siblings(".size_qty").val();
    var size_price = $(this).siblings(".size_price").val();
    var qty = parseInt($("#qty" + itemid).val());
    var minimum_qty = $(this).siblings(".minimum_qty").val();

    $(".gocover").show();
    if (qty <= 1) {
      $("#qty" + itemid).val("1");
      $(".gocover").hide();
      return false;
    } else if (qty < minimum_qty) {
      $(".gocover").hide();
      return false;
    } else {
      $(".gocover").show();

      $("#qty" + itemid).val(qty);
      $.ajax({
        type: "GET",
        url: mainurl + "/reducebyone",
        timeout: 5000,
        data: {
          id: pid,
          itemid: itemid,
          size_qty: size_qty,
          size_price: size_price,
        },
        success: function (data) {
          $(".gocover").hide();
          if (data.qty >= 1) {
            $.ajax({
              url: mainurl + "/carts",
              type: 'GET',
              timeout: 5000,
              success: function (response) {
                $(".load_cart").html(response);
              },
              error: function () {
                toastr.error("Failed to update cart. Please refresh the page.");
              },
              complete: function () {
                $(".gocover").hide();
              }
            });
          } else {
            $(".gocover").hide();
            return false;
          }
        },
        error: function (xhr, status, error) {
          $(".gocover").hide();
          console.error("Quantity update error:", error);
          toastr.error("Failed to update quantity. Please try again.");
        },
        complete: function () {
          $(".gocover").hide();
        }
      });
    }
  });

  $(document).on("click", ".cart_size", function () {
    let qty = $(this).data("qty");
    $("#stock").val(qty);
    updateProductPrice();
  });
  $(document).on("click", ".cart_color", function () {
    updateProductPrice();
  });
  $(document).on("click", ".cart_attr", function () {
    updateProductPrice();
  });

  function updateProductPrice() {
    let size_price = $(".cart_size input:checked").attr("data-price");
    let color_price = $(".cart_color input:checked").attr("data-price");
    let attr_price = $(".cart_attr:checked")
      .map(function () {
        return $(this).data("price");
      })
      .get()
      .reduce((a, b) => a + b, 0);
    let main_price = $("#product_price").val();

    if (size_price == undefined) {
      size_price = 0;
    }
    if (color_price == undefined) {
      color_price = 0;
    }

    let total =
      parseFloat(size_price) +
      parseFloat(color_price) +
      parseFloat(attr_price) +
      parseFloat(main_price);

    var pos = $("#curr_pos").val();
    var sign = $("#curr_sign").val();
    if (pos == "0") {
      $("#sizeprice").html(sign + total);
    } else {
      $("#sizeprice").html(total + sign);
    }
  }

  $(document).on("click", "#addtodetailscart", function (e) {
    if (!commerceCan("cart")) {
      e.preventDefault();
      commerceBlocked("Add to cart");
      return false;
    }
    let pid = "";
    let qty = "";
    let size_key = "";
    let size = "";
    let size_qty = "";
    let size_price = "";
    let color = "";
    let color_price = "";
    let values = "";
    let keys = "";
    let prices = "";

    // get all the input values
    pid = $("#product_id").val();
    qty = $("#order-qty").val();
    size_key = $(".cart_size input:checked").val();
    size = $(".cart_size input:checked").attr("data-key");
    size_qty = $(".cart_size input:checked").attr("data-qty");
    size_price = $(".cart_size input:checked").attr("data-price");
    color = $(".cart_color input:checked").attr("data-color");
    color_price = $(".cart_color input:checked").attr("data-price");
    values = $(".cart_attr:checked")
      .map(function () {
        return $(this).val();
      })
      .get();
    keys = $(".cart_attr:checked")
      .map(function () {
        return $(this).attr("data-key");
      })
      .get();
    prices = $(".cart_attr:checked")
      .map(function () {
        return $(this).attr("data-price");
      })
      .get();

    $(".gocover").show(); // Show loader at start

    $.ajax({
      type: "GET",
      url: mainurl + "/addnumcart",
      timeout: 5000,
      data: {
        id: pid,
        qty: qty,
        size: size,
        color: color,
        color_price: color_price,
        size_qty: size_qty,
        size_price: size_price,
        size_key: size_key,
        keys: keys,
        values: values,
        prices: prices,
      },
      success: function (data) {
        $(".gocover").hide();
        if (data == "digital") {
          toastr.error("Already Added To Cart");
        } else if (data == 0) {
          toastr.error("Out Of Stock");
        } else if (data[3]) {
          toastr.error(lang.minimum_qty_error + " " + data[4]);
        } else {
          $("#cart-count").html(data[0]);
          $("#cart-count1").html(data[0]);
          $(".cart-popup").load(mainurl + "/carts/view");
          $("#cart-items").load(mainurl + "/carts/view");
          toastr.success("Successfully Added To Cart");
        }
      },
      error: function (xhr, status, error) {
        $(".gocover").hide();
        console.error("Add to cart error:", error);
        if (status === "timeout") {
          toastr.error("Request timed out. Please try again.");
        } else {
          toastr.error("Failed to add item to cart. Please try again.");
        }
      },
      complete: function () {
        $(".gocover").hide();
      }
    });
  });

  $(document).on("click", "#addtobycard", function () {
    if (!commerceCan("cart")) {
      commerceBlocked("Add to cart");
      return false;
    }
    let pid = "";
    let qty = "";
    let size_key = "";
    let size = "";
    let size_qty = "";
    let size_price = "";
    let color = "";
    let color_price = "";
    let values = "";
    let keys = "";
    let prices = "";

    // get all the input values
    pid = $("#product_id").val();
    qty = $("#order-qty").val();
    size_key = $(".cart_size input:checked").val();
    size = $(".cart_size input:checked").attr("data-key");
    size_qty = $(".cart_size input:checked").attr("data-qty");
    size_price = $(".cart_size input:checked").attr("data-price");
    color = $(".cart_color input:checked").attr("data-color");

    if (size_key == undefined) {
      size_key = "";
    }
    if (size == undefined) {
      size = "";
    }
    if (size_qty == undefined) {
      size_qty = "";
    }

    if (color != undefined) {
      color = color.replace("#", "");
    } else {
      color = "";
    }

    color_price = $(".cart_color input:checked").attr("data-price");
    values = $(".cart_attr:checked")
      .map(function () {
        return $(this).val();
      })
      .get();
    keys = $(".cart_attr:checked")
      .map(function () {
        return $(this).attr("data-key");
      })
      .get();
    prices = $(".cart_attr:checked")
      .map(function () {
        return $(this).attr("data-price");
      })
      .get();

    window.location =
      mainurl +
      "/addtonumcart?id=" +
      pid +
      "&qty=" +
      qty +
      "&size=" +
      size +
      "&color=" +
      color +
      "&color_price=" +
      color_price +
      "&size_qty=" +
      size_qty +
      "&size_price=" +
      size_price +
      "&size_key=" +
      size_key +
      "&keys=" +
      keys +
      "&values=" +
      values +
      "&prices=" +
      prices;
  });

  /**
   * Theme 4 Category page sliders (scoped; no global slick changes)
   * - auto-slide
   * - pause on hover
   * - responsive breakpoints
   * - section arrows wired to each row
   * - initialize only once
   */
  function initTheme4CategorySliders() {
    var $page = $(".t4-category-page");
    if (!$page.length) return;
    if (!$.fn || !$.fn.slick) return;

    // Product rows
    $page.find(".t4-product-slider").each(function () {
      var $slider = $(this);
      if ($slider.hasClass("slick-initialized")) return;

      $slider.slick({
        dots: false,
        arrows: false,
        infinite: true,
        speed: 450,
        slidesToShow: 4,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 2600,
        pauseOnHover: true,
        pauseOnFocus: true,
        adaptiveHeight: false,
        responsive: [
          { breakpoint: 1200, settings: { slidesToShow: 3 } },
          { breakpoint: 768, settings: { slidesToShow: 2 } },
          { breakpoint: 480, settings: { slidesToShow: 1 } },
        ],
      });
    });

    // Brands row (feature block)
    $page.find(".t4-brands__slider").each(function () {
      var $slider = $(this);
      if ($slider.hasClass("slick-initialized")) return;

      $slider.slick({
        dots: false,
        arrows: false,
        infinite: true,
        speed: 450,
        slidesToShow: 8,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 2200,
        pauseOnHover: true,
        pauseOnFocus: true,
        adaptiveHeight: false,
        responsive: [
          { breakpoint: 1200, settings: { slidesToShow: 6 } },
          { breakpoint: 768, settings: { slidesToShow: 4 } },
          { breakpoint: 480, settings: { slidesToShow: 3 } },
        ],
      });
    });

    // Wire section arrows to the nearest product slider
    $page.off("click.t4slider");
    $page.on("click.t4slider", ".t4-section .t4-nav-btn", function () {
      var dir = $(this).data("t4-dir");
      var $section = $(this).closest(".t4-section");
      var $slider = $section.find(".t4-product-slider").first();
      if (!$slider.length || !$slider.hasClass("slick-initialized")) return;
      if (dir === "prev") $slider.slick("slickPrev");
      if (dir === "next") $slider.slick("slickNext");
    });
  }

  $(function () {
    initTheme4CategorySliders();
  });

  // Defensive: sometimes sliders render before all assets settle (especially after bfcache/back nav).
  // Re-run once after window load to ensure slick-initialized is applied.
  $(window).on("load", function () {
    initTheme4CategorySliders();
  });
})(jQuery);
