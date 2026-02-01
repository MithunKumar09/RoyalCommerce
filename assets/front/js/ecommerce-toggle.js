/* global toastr */
(function () {
  "use strict";

  var state = null;
  var lastMode = null;
  var lastCaps = null;

  function safeJsonParse(str) {
    try {
      return JSON.parse(str);
    } catch (e) {
      return null;
    }
  }

  function can(capability) {
    if (!state || !state.capabilities) return true;
    return !!state.capabilities[capability];
  }

  function whatsappLink(payloadText) {
    var number = state && state.whatsapp ? state.whatsapp.number : null;
    if (!number) return null;
    var digits = String(number).replace(/[^\d]/g, "");
    if (!digits) return null;
    var text = payloadText || "Hi, I want to place an order.";
    return "https://wa.me/" + digits + "?text=" + encodeURIComponent(text);
  }

  function buildWhatsAppMessage(actionLabel) {
    var lines = [];

    if (actionLabel) lines.push(String(actionLabel));

    // Best-effort: pull product selections from existing DOM used by myscript.js.
    var pidEl = document.querySelector("#product_id");
    if (pidEl && pidEl.value) lines.push("Product ID: " + pidEl.value);

    var qtyEl = document.querySelector("#order-qty");
    if (qtyEl && qtyEl.value) lines.push("Qty: " + qtyEl.value);

    var sizeEl = document.querySelector(".cart_size input:checked");
    if (sizeEl) {
      var sizeLabel = sizeEl.getAttribute("data-key") || sizeEl.value;
      if (sizeLabel) lines.push("Size: " + sizeLabel);
    }

    var colorEl = document.querySelector(".cart_color input:checked");
    if (colorEl) {
      var colorLabel = colorEl.getAttribute("data-color") || colorEl.value;
      if (colorLabel) lines.push("Color: " + colorLabel);
    }

    var attrEls = document.querySelectorAll(".cart_attr:checked");
    if (attrEls && attrEls.length) {
      attrEls.forEach(function (el) {
        var k = el.getAttribute("data-key");
        var v = el.value;
        if (k && v) lines.push(k + ": " + v);
      });
    }

    // Fallback attribute patterns on some themes.
    var attrElsAlt = document.querySelectorAll(".product-attr:checked, .product_attr:checked");
    if (attrElsAlt && attrElsAlt.length) {
      attrElsAlt.forEach(function (el) {
        var k = el.getAttribute("data-key") || el.name;
        var v = el.value;
        if (k && v) lines.push(k + ": " + v);
      });
    }

    lines.push(document.title);
    lines.push(window.location.href);

    return lines.filter(Boolean).join("\n");
  }

  var cartCtaSelectors = [
    ".add_cart_click",
    "#addtodetailscart",
    "#addtobycard",
    "#addcrt",
    "#qaddcrt",
    ".add_to_cart_button",
    ".single_add_to_cart_button",
    ".btn-add-cart",
    ".btn-buy-now",
    ".btn-buy-now-sm",
    ".btn-add-cart-sm",
    ".quantity-up",
    ".quantity-down",
  ];

  var checkoutSelectors = ['a[href*="/checkout"]', ".checkout"];

  var cartEntrySelectors = [
    'a[href*="/cart"]',
    ".cart-popup",
    "#cart-count",
    "#cart-count1",
    ".cart-icon",
    ".header-cart-count",
    ".t4-iconlink",
  ];

  var orderSelectors = ['a[href*="/user/orders"]', 'a[href*="/user/order"]'];

  var trackSelectors = ['a[href*="/track"]', ".t4-track__title", "#order-track"];

  var walletSelectors = ['[data-val="wallet"]', '[data-payment="wallet"]', ".payment-wallet"];

  function getBaseUrl() {
    var base = "";
    try {
      if (typeof mainurl !== "undefined" && mainurl) {
        base = String(mainurl);
      }
    } catch (_) {}
    base = base.replace(/\/+$/, "");
    return base;
  }

  function getStateUrl() {
    var base = getBaseUrl();
    return (base ? base : "") + "/ecommerce/state";
  }

  function getStreamUrl() {
    var base = getBaseUrl();
    return (base ? base : "") + "/ecommerce/stream";
  }

  function getNodesFromSelectors(selectors) {
    var nodes = [];
    var seen = new Set();
    selectors.forEach(function (sel) {
      document.querySelectorAll(sel).forEach(function (el) {
        if (!seen.has(el)) {
          seen.add(el);
          nodes.push(el);
        }
      });
    });
    return nodes;
  }

  function setHidden(el, hidden) {
    if (hidden) {
      el.classList.add("commerce-hidden");
      el.setAttribute("aria-hidden", "true");
    } else {
      el.classList.remove("commerce-hidden");
      el.removeAttribute("aria-hidden");
    }
  }

  function setDisabled(el, disabled) {
    if (disabled) {
      el.classList.add("ecommerce-disabled");
      el.setAttribute("aria-disabled", "true");
      el.setAttribute("data-ecommerce-disabled", "1");
    } else {
      el.classList.remove("ecommerce-disabled");
      el.removeAttribute("aria-disabled");
      el.removeAttribute("data-ecommerce-disabled");
    }
  }

  function canOpenWhatsApp() {
    // Avoid injecting a dead CTA if number is missing.
    return !!whatsappLink("ping");
  }

  function createWhatsAppButton() {
    var btn = document.createElement("button");
    btn.type = "button";
    // Keep existing styling class + add required anti-dup class.
    btn.className = "whatsapp-order-btn wa-order-btn";
    btn.setAttribute("data-ecommerce-whatsapp", "1");
    btn.innerHTML = `
<svg
  width="20"
  height="20"
  viewBox="0 0 32 32"
  fill="currentColor"
  aria-hidden="true"
  style="margin-right:6px;"
>
  <path d="M16.02 3C9.4 3 4 8.3 4 14.84c0 2.6.9 5.02 2.43 6.97L4 29l7.4-2.37a12.1 12.1 0 0 0 4.62.9c6.62 0 12.02-5.3 12.02-11.84C28.04 8.3 22.64 3 16.02 3zm0 21.7c-1.42 0-2.8-.3-4.06-.88l-.29-.13-4.4 1.4 1.43-4.22-.18-.28a9.7 9.7 0 0 1-1.55-5.25c0-5.34 4.43-9.69 9.05-9.69 4.62 0 9.05 4.35 9.05 9.69 0 5.34-4.43 9.69-9.05 9.69z"/>
  <path d="M21.44 18.6c-.3-.15-1.77-.87-2.04-.97-.27-.1-.47-.15-.66.15-.2.3-.76.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.27-.46-2.41-1.47-.9-.8-1.5-1.78-1.68-2.08-.17-.3-.02-.46.13-.6.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.66-1.6-.9-2.2-.23-.56-.46-.48-.66-.49h-.56c-.2 0-.5.07-.76.37-.27.3-1 1-1 2.45s1.03 2.85 1.17 3.05c.15.2 2.02 3.08 4.9 4.32.69.3 1.23.48 1.65.61.69.22 1.32.19 1.82.12.56-.08 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.42-.07-.12-.27-.2-.56-.35z"/>
</svg>
<span>Order Now</span>
`;
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      var wa = whatsappLink(buildWhatsAppMessage("Order Now"));
      if (wa) {
        window.open(wa, "_blank", "noopener");
      }
    });
    return btn;
  }

  function createWhatsAppButtonEnabledProductDetail() {
    var btn = document.createElement("button");
    btn.type = "button";
    btn.className = "whatsapp-order-btn wa-order-btn-enabled w-100";
    btn.setAttribute("data-ecommerce-whatsapp-enabled", "1");
    btn.innerHTML = `
<svg
  width="20"
  height="20"
  viewBox="0 0 32 32"
  fill="currentColor"
  aria-hidden="true"
  style="margin-right:6px;"
>
  <path d="M16.02 3C9.4 3 4 8.3 4 14.84c0 2.6.9 5.02 2.43 6.97L4 29l7.4-2.37a12.1 12.1 0 0 0 4.62.9c6.62 0 12.02-5.3 12.02-11.84C28.04 8.3 22.64 3 16.02 3zm0 21.7c-1.42 0-2.8-.3-4.06-.88l-.29-.13-4.4 1.4 1.43-4.22-.18-.28a9.7 9.7 0 0 1-1.55-5.25c0-5.34 4.43-9.69 9.05-9.69 4.62 0 9.05 4.35 9.05 9.69 0 5.34-4.43 9.69-9.05 9.69z"/>
  <path d="M21.44 18.6c-.3-.15-1.77-.87-2.04-.97-.27-.1-.47-.15-.66.15-.2.3-.76.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.27-.46-2.41-1.47-.9-.8-1.5-1.78-1.68-2.08-.17-.3-.02-.46.13-.6.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.66-1.6-.9-2.2-.23-.56-.46-.48-.66-.49h-.56c-.2 0-.5.07-.76.37-.27.3-1 1-1 2.45s1.03 2.85 1.17 3.05c.15.2 2.02 3.08 4.9 4.32.69.3 1.23.48 1.65.61.69.22 1.32.19 1.82.12.56-.08 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.42-.07-.12-.27-.2-.56-.35z"/>
</svg>
<span>Order Now</span>
`;
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      var wa = whatsappLink(buildWhatsAppMessage("Order Now"));
      if (wa) {
        window.open(wa, "_blank", "noopener");
      }
    });
    return btn;
  }

  function findCtaContainer(el) {
    if (!el || !el.closest) return null;
    // Prefer known CTA wrappers; fall back to a safe parent.
    return (
      el.closest(".cta-buttons-section") ||
      el.closest(".product-actions") ||
      el.closest(".product-action") ||
      el.closest(".product-actions-wrapper") ||
      el.parentElement
    );
  }

  function injectWhatsAppButtons() {
    if (!canOpenWhatsApp()) return;

    var containers = new Set();
    getNodesFromSelectors(cartCtaSelectors).forEach(function (el) {
      var c = findCtaContainer(el);
      if (c) containers.add(c);
    });

    containers.forEach(function (container) {
      // Idempotent: never inject twice per container.
      if (container.querySelector && container.querySelector('.wa-order-btn[data-ecommerce-whatsapp="1"]')) return;

      // Only if container has at least one commerce CTA we manage.
      var hasAny = false;
      cartCtaSelectors.forEach(function (sel) {
        if (!hasAny && container.querySelector && container.querySelector(sel)) hasAny = true;
      });
      if (!hasAny) return;

      // Hide/disable only commerce CTAs inside this container (do NOT touch wishlist etc).
      cartCtaSelectors.forEach(function (sel) {
        if (!container.querySelectorAll) return;
        container.querySelectorAll(sel).forEach(function (el) {
          setHidden(el, true);
          setDisabled(el, true);
        });
      });

      // Insert after the last CTA in the container (or append if unknown).
      var last = null;
      cartCtaSelectors.forEach(function (sel) {
        if (!container.querySelectorAll) return;
        var nodes = container.querySelectorAll(sel);
        if (nodes && nodes.length) last = nodes[nodes.length - 1];
      });

      var waBtn = createWhatsAppButton();
      if (last && last.insertAdjacentElement) {
        last.insertAdjacentElement("afterend", waBtn);
      } else {
        container.appendChild(waBtn);
      }
    });
  }

  function cleanupWhatsAppButtons() {
    document.querySelectorAll('.wa-order-btn[data-ecommerce-whatsapp="1"]').forEach(function (el) {
      if (el && el.parentNode) el.parentNode.removeChild(el);
    });
  }

  function cleanupEnabledProductDetailWhatsAppButton() {
    document.querySelectorAll('[data-ecommerce-whatsapp-enabled="1"]').forEach(function (el) {
      if (el && el.parentNode) el.parentNode.removeChild(el);
    });
  }

  function injectEnabledProductDetailWhatsAppButton() {
    if (!canOpenWhatsApp()) return;

    // Strict scope: only the product detail page right CTA box.
    // Identified by `.cta-buttons-section` containing the existing `#addtodetailscart` button.
    var container = document.querySelector('.cta-buttons-section #addtodetailscart')
      ? document.querySelector('.cta-buttons-section')
      : null;
    if (!container) return;

    if (container.querySelector('.wa-order-btn-enabled[data-ecommerce-whatsapp-enabled="1"]')) return;

    var anchor = container.querySelector("#addtodetailscart") || container.lastElementChild;
    var btn = createWhatsAppButtonEnabledProductDetail();
    if (anchor && anchor.insertAdjacentElement) {
      anchor.insertAdjacentElement("afterend", btn);
    } else {
      container.appendChild(btn);
    }
  }

  function toastInfoOnce(message) {
    if (!window.toastr) return;
    toastr.options = toastr.options || {};
    toastr.options.timeOut = 3000;
    toastr.info(message);
  }

  function applyUi() {
    var mode = state ? state.mode : "enabled";

    // Add simple marker for CSS hooks/debugging.
    try {
      document.documentElement.setAttribute("data-commerce-mode", mode);
    } catch (_) {}

    var enabledMode = mode === "enabled";
    var cartEnabled = enabledMode && can("cart");
    var checkoutEnabled = enabledMode && can("checkout") && can("orders");
    var ordersEnabled = enabledMode && can("orders");
    var trackingEnabled = enabledMode && can("order_tracking");
    var walletEnabled = enabledMode && can("wallet");

    getNodesFromSelectors(cartCtaSelectors).forEach(function (el) {
      setHidden(el, !cartEnabled);
      setDisabled(el, !cartEnabled);
    });

    getNodesFromSelectors(checkoutSelectors).forEach(function (el) {
      setHidden(el, !checkoutEnabled);
      setDisabled(el, !checkoutEnabled);
    });

    getNodesFromSelectors(cartEntrySelectors).forEach(function (el) {
      setHidden(el, !cartEnabled);
      setDisabled(el, !cartEnabled);
    });

    getNodesFromSelectors(orderSelectors).forEach(function (el) {
      setHidden(el, !ordersEnabled);
      setDisabled(el, !ordersEnabled);
    });

    getNodesFromSelectors(trackSelectors).forEach(function (el) {
      setHidden(el, !trackingEnabled);
      setDisabled(el, !trackingEnabled);
    });

    getNodesFromSelectors(walletSelectors).forEach(function (el) {
      setHidden(el, !walletEnabled);
      setDisabled(el, !walletEnabled);
    });

    if (mode === "whatsapp_only") {
      cleanupEnabledProductDetailWhatsAppButton();
      cleanupWhatsAppButtons();
      injectWhatsAppButtons();
    } else {
      cleanupWhatsAppButtons();
      cleanupEnabledProductDetailWhatsAppButton();
      if (mode === "enabled") {
        injectEnabledProductDetailWhatsAppButton();
      }
    }
  }

  // Capture-phase blocker for any disabled element (prevents other handlers firing).
  document.addEventListener(
    "click",
    function (e) {
      var target = e.target && e.target.closest ? e.target.closest('[data-ecommerce-disabled="1"]') : null;
      if (!target) return;

      var st = state || {};
      var mode = st.mode || "enabled";

      // Always stop navigation/actions.
      e.preventDefault();
      e.stopPropagation();
      if (typeof e.stopImmediatePropagation === "function") {
        e.stopImmediatePropagation();
      }

      if (mode === "whatsapp_only") {
        var actionLabel = null;
        try {
          if (target.matches && (target.matches(".add_cart_click") || target.matches("#addtodetailscart") || target.matches("#addtobycard"))) {
            actionLabel = "Add to cart";
          } else if (target.getAttribute) {
            var href = target.getAttribute("href") || "";
            if (href.indexOf("/checkout") !== -1) actionLabel = "Checkout";
          }
        } catch (_) {}

        var wa = whatsappLink(buildWhatsAppMessage(actionLabel || "Order request"));
        if (wa) {
          window.open(wa, "_blank", "noopener");
          return;
        }
      }

      toastInfoOnce(mode === "disabled" ? "Store is temporarily unavailable." : "This feature is unavailable.");
    },
    true
  );

  function onStateUpdate(next) {
    if (!next) return;
    state = next;

    var mode = state.mode || "enabled";
    var caps = state.capabilities || {};

    // Notify only on meaningful changes.
    var capsKey = JSON.stringify(caps);
    if (lastMode !== null && (mode !== lastMode || capsKey !== lastCaps)) {
      if (mode === "disabled") {
        toastInfoOnce("Store is temporarily unavailable.");
      } else if (mode === "whatsapp_only") {
        toastInfoOnce("Ordering is available via WhatsApp.");
      } else {
        toastInfoOnce("Store is back online.");
      }
    }

    lastMode = mode;
    lastCaps = capsKey;

    applyUi();
  }

  // Expose a tiny API for other scripts (myscript.js checks this).
  window.Commerce = {
    can: can,
    state: function () {
      return state;
    },
    whatsappLink: whatsappLink,
    buildWhatsAppMessage: buildWhatsAppMessage,
    applyUi: applyUi,
  };

  var pollingTimer = null;
  var sseErrorLogged = false;

  function startPolling() {
    if (pollingTimer) return;
    pollingTimer = window.setInterval(function () {
      fetchStateOnce();
    }, 12000);
  }

  function stopPolling() {
    if (pollingTimer) {
      window.clearInterval(pollingTimer);
      pollingTimer = null;
    }
  }

  // SSE first (instant updates)
  function startSse() {
    if (!window.EventSource) return false;
    try {
      var es = new EventSource(getStreamUrl());
      es.addEventListener("commerce_state", function (evt) {
        onStateUpdate(safeJsonParse(evt.data));
      });
      // If server sends default message events, handle them too.
      es.onmessage = function (evt) {
        onStateUpdate(safeJsonParse(evt.data));
      };
      es.onerror = function () {
        if (!sseErrorLogged && window.console && typeof console.warn === "function") {
          console.warn("Ecommerce SSE connection error. Falling back to polling.");
          sseErrorLogged = true;
        }
        startPolling();
      };
      return true;
    } catch (e) {
      return false;
    }
  }

  // Fallback: one-time fetch (and optionally short polling later)
  function fetchStateOnce() {
    try {
      fetch(getStateUrl(), { credentials: "same-origin" })
        .then(function (r) {
          return r.json();
        })
        .then(function (json) {
          onStateUpdate(json);
        })
        .catch(function () {});
    } catch (e) {}
  }

  var sseStarted = startSse();
  if (!sseStarted) {
    fetchStateOnce();
    startPolling();
  } else {
    // Still fetch once quickly to populate if the first SSE event is delayed.
    fetchStateOnce();
    stopPolling();
  }

  function debounce(fn, wait) {
    var t = null;
    return function () {
      if (t) window.clearTimeout(t);
      t = window.setTimeout(fn, wait);
    };
  }

  var debouncedApplyUi = debounce(function () {
    if (state) applyUi();
  }, 250);

  if (window.MutationObserver) {
    try {
      var observer = new MutationObserver(function () {
        debouncedApplyUi();
      });
      observer.observe(document.body, { childList: true, subtree: true });
    } catch (_) {}
  }

  if (window.jQuery && window.jQuery(document).ajaxComplete) {
    window.jQuery(document).ajaxComplete(function () {
      debouncedApplyUi();
    });
  }
})();

