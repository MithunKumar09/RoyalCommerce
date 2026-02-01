/* Product media modals (360 / 3D / YouTube) - lazy init
 * Loaded ONLY on product page.
 */
(function () {
  'use strict';

  // Bootstrap 5 required for modal events + API.
  function getBootstrap() {
    return window.bootstrap;
  }

  function el(html) {
    const tpl = document.createElement('template');
    tpl.innerHTML = html.trim();
    return tpl.content.firstChild;
  }

  function showLoading(container, message) {
    container.innerHTML = '';
    container.appendChild(
      el(
        `<div class="d-flex flex-column align-items-center justify-content-center" style="min-height:500px;">
          <div class="spinner-border text-danger" role="status" aria-label="Loading"></div>
          <div class="mt-3 text-muted">${message}</div>
        </div>`
      )
    );
  }

  function showError(container, message) {
    container.innerHTML = '';
    container.appendChild(
      el(
        `<div class="d-flex flex-column align-items-center justify-content-center" style="min-height:500px; text-align:center;">
          <div class="text-danger fw-bold">${message}</div>
          <div class="mt-2 text-muted">Please try again.</div>
        </div>`
      )
    );
  }

  // -----------------------
  // YouTube modal
  // -----------------------
  function parseYouTubeId(url) {
    if (!url || typeof url !== 'string') return null;
    const patterns = [
      /(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/,
      /youtube\.com\/embed\/([^&\n?#]+)/,
    ];
    for (const p of patterns) {
      const m = url.match(p);
      if (m && m[1]) return m[1];
    }
    return null;
  }

  function openVideoModal(url) {
    const modalEl = document.getElementById('modalVideoView');
    const bs = getBootstrap();
    if (!modalEl || !bs) return;

    const container = modalEl.querySelector('.js-media-video');
    if (!container) return;

    if (!url) return;
    container.dataset.videoUrl = url || '';
    bs.Modal.getOrCreateInstance(modalEl).show();
  }

  function initVideoModal() {
    const modalEl = document.getElementById('modalVideoView');
    const bs = getBootstrap();
    if (!modalEl || !bs) return;

    modalEl.addEventListener('shown.bs.modal', function () {
      const container = modalEl.querySelector('.js-media-video');
      if (!container) return;

      const url = container.dataset.videoUrl || '';
      const id = parseYouTubeId(url);
      if (id) {
        // Lazy inject iframe
        showLoading(container, 'Loading video...');
        const iframe = el(
          `<iframe
            width="100%"
            height="500"
            src="https://www.youtube.com/embed/${id}?autoplay=1&rel=0&modestbranding=1"
            title="YouTube video player"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen
          ></iframe>`
        );
        container.innerHTML = '';
        container.appendChild(iframe);
        return;
      }

      // Fallback: render native video for direct sources
      const video = el(
        `<video controls autoplay style="width:100%; max-height:500px; background:#000;">
          <source src="${url}">
          Your browser does not support the video tag.
        </video>`
      );
      container.innerHTML = '';
      container.appendChild(video);
    });

    modalEl.addEventListener('hidden.bs.modal', function () {
      const container = modalEl.querySelector('.js-media-video');
      if (!container) return;
      // Stop playback by removing iframe
      container.innerHTML = '';
      container.dataset.videoUrl = '';
    });

    // Bind triggers (top-right Play button and video thumbnail)
    document.addEventListener('click', function (e) {
      const playBtn = e.target.closest('.play-video-btn');
      if (playBtn) {
        e.preventDefault();
        openVideoModal(playBtn.getAttribute('data-video-url') || '');
        return;
      }

      const videoThumb = e.target.closest('.video-thumbnail');
      if (videoThumb) {
        e.preventDefault();
        openVideoModal(videoThumb.getAttribute('data-video-url') || '');
      }
    });
  }

  // -----------------------
  // 3D modal (model-viewer)
  // -----------------------
  let modelViewerLoaded = false;
  let modelViewerLoading = null;

  function loadModelViewer() {
    if (modelViewerLoaded) return Promise.resolve();
    if (modelViewerLoading) return modelViewerLoading;

    modelViewerLoading = new Promise((resolve, reject) => {
      const script = document.createElement('script');
      script.type = 'module';
      script.src = 'https://cdn.jsdelivr.net/npm/@google/model-viewer/dist/model-viewer.min.js';
      script.onload = () => {
        modelViewerLoaded = true;
        resolve();
      };
      script.onerror = () => reject(new Error('Failed to load model-viewer'));
      document.head.appendChild(script);
    });

    return modelViewerLoading;
  }

  function createHotspotDot(hs) {
    const label = (hs && hs.label) ? String(hs.label) : 'Hotspot';
    const desc = (hs && hs.description) ? String(hs.description) : '';
    const x = (hs && typeof hs.x_percent === 'number') ? hs.x_percent : 0;
    const y = (hs && typeof hs.y_percent === 'number') ? hs.y_percent : 0;
    const imageUrlRaw = (hs && hs.image_url) ? String(hs.image_url) : '';
    const imageUrl = imageUrlRaw ? resolveMediaUrl(imageUrlRaw) : '';

    const dot = el(
      `<div class="hotspot-dot" style="left:${x}%; top:${y}%;" data-hotspot-target="${hs.target || ''}" data-hotspot-id="${hs.id || ''}">
        <span class="dot-pulse"></span>
        <div class="hotspot-tooltip">
          <strong>${label}</strong>
          ${desc ? `<p>${desc}</p>` : ``}
          ${imageUrl ? `<div style="margin-top:8px;"><img src="${imageUrl}" alt="${label}" style="max-width:100%; border-radius:6px;"></div>` : ``}
        </div>
      </div>`
    );
    return dot;
  }

  function getAppBaseHref() {
    const candidate =
      (typeof window.mainurl === 'string' && window.mainurl.trim())
        ? window.mainurl.trim()
        : window.location.origin;
    try {
      const u = new URL(candidate, window.location.origin);
      u.hash = '';
      u.search = '';
      if (!u.pathname.endsWith('/')) u.pathname += '/';
      return u.href;
    } catch (_) {
      return window.location.origin.replace(/\/?$/, '/');
    }
  }

  // Helper function to resolve media URLs (production-safe)
  // CRITICAL: This function MUST catch invalid URLs like "https://assets/..." FIRST
  function resolveMediaUrl(path) {
    if (!path || typeof path !== 'string') return '';

    // Normalize weird escaping coming from JSON (e.g. "/\\/assets/..." or "\\/assets/...")
    // - convert "\/" to "/"
    // - convert "/\/" to "//" then handle "//assets" below
    path = path.replace(/\\\//g, '/').trim();
    const baseHref = getAppBaseHref();
    let baseUrl;
    try {
      baseUrl = new URL(baseHref);
    } catch (_) {
      baseUrl = new URL(window.location.origin + '/');
    }

    // Handle protocol-relative URLs created by strings like "//assets/..."
    // Browsers treat "//assets/..." as "https://assets/..." which is invalid for us.
    if (path.startsWith('//assets/')) {
      path = path.replace(/^\/\/assets\//, 'assets/');
    }

    // Allow data/blob URLs as-is
    if (path.startsWith('data:') || path.startsWith('blob:')) {
      return path;
    }
    
    // CRITICAL FIX #1: Handle invalid URLs like "https://assets/path" IMMEDIATELY
    // This is the most common production issue - catch it before any other processing
    if (path.match(/^https?:\/\/assets\//)) {
      // Extract everything after "https://assets" and make it a relative path
      const relativePath = path.replace(/^https?:\/\/assets\/?/, 'assets/');
      try {
        const resolved = new URL(relativePath, baseHref).href;
        console.log('[resolveMediaUrl] Fixed invalid URL:', { from: path, to: resolved });
        return resolved;
      } catch (e) {
        console.warn('[resolveMediaUrl] Failed to resolve:', path, e);
        return relativePath; // Fallback to relative path
      }
    }
    
    // If already a full URL with protocol, validate and return
    if (path.startsWith('http://') || path.startsWith('https://')) {
      try {
        const url = new URL(path);
        // If URL has a valid hostname (including localhost), return it
        if (url.hostname && url.hostname !== 'assets') {
          return url.href;
        }
        // If it's like "https://assets/..." (invalid), extract path and fix it
        if (url.hostname === 'assets') {
          let pathOnly = (url.pathname || '').replace(/^\/+/, '');
          if (!pathOnly.startsWith('assets/')) pathOnly = 'assets/' + pathOnly;
          return new URL(pathOnly, baseHref).href;
        }
      } catch (e) {
        // Invalid URL, try to extract path and fix it
        // Pattern: https://assets/path/to/file -> /assets/path/to/file
        const pathMatch = path.match(/https?:\/\/assets(\/.*)/);
        if (pathMatch && pathMatch[1]) {
          const normalizedPath = ('assets' + pathMatch[1]).replace(/^\/+/, '');
          try {
            return new URL(normalizedPath, baseHref).href;
          } catch (e2) {
            // Fall through to relative path handling
          }
        }
      }
    }

    // Handle other protocol-relative URLs (e.g. "//cdn.example.com/...") safely
    if (path.startsWith('//')) {
      try {
        return new URL(window.location.protocol + path).href;
      } catch (_) {
        // Fall through
      }
    }
    
    // If path is prefixed with the app base path (subdirectory deployments), strip it.
    const appPath = baseUrl.pathname.replace(/\/$/, ''); // "" or "/something"
    if (appPath && path.startsWith(appPath + '/')) {
      path = path.slice(appPath.length + 1);
    }

    // Normalize relative paths for base-url resolution (must be relative, not absolute-to-domain)
    const normalizedPath = path.replace(/^\/+/, '');
    
    // Resolve against app base (works for root + subdirectory)
    try {
      return new URL(normalizedPath, baseHref).href;
    } catch (e) {
      // Fallback: return as-is if URL construction fails
      return normalizedPath;
    }
  }

  function init3DModal() {
    const modalEl = document.getElementById('modal3DView');
    const bs = getBootstrap();
    if (!modalEl || !bs) return;

    modalEl.addEventListener('shown.bs.modal', function () {
      const container = modalEl.querySelector('.js-media-3d');
      if (!container) return;
      const modelUrlRaw = container.getAttribute('data-model-url') || '';
      const viewerJson = container.getAttribute('data-viewer') || '{}';
      const hotspotsJson = container.getAttribute('data-hotspots') || '[]';

      if (!modelUrlRaw) {
        showError(container, '3D model not available.');
        return;
      }

      // Resolve URL properly for production
      let modelUrl = resolveMediaUrl(modelUrlRaw);
      
      // Debug logging - always log to help diagnose issues
      console.log('[3D Modal] URL resolution:', { 
        raw: modelUrlRaw, 
        resolved: modelUrl,
        origin: window.location.origin 
      });
      
      // CRITICAL: Double-check - if still invalid, force fix
      if (modelUrl.includes('://assets/')) {
        console.error('[3D Modal] CRITICAL: URL still invalid after resolution!', modelUrl);
        // Force fix one more time
        const forcedFix = modelUrl.replace(/https?:\/\/assets/, '/assets');
        modelUrl = resolveMediaUrl(forcedFix);
        console.log('[3D Modal] Forced fix applied:', modelUrl);
      }

      let opts = {};
      try {
        opts = JSON.parse(viewerJson);
      } catch (_) {
        opts = {};
      }

      showLoading(container, 'Loading 3D model...');

      loadModelViewer()
        .then(() => {
          // Parse hotspots (admin-normalized items with x_percent/y_percent)
          let hotspots = [];
          try {
            hotspots = JSON.parse(hotspotsJson) || [];
          } catch (_) {
            hotspots = [];
          }
          const modelHotspots = Array.isArray(hotspots) ? hotspots.filter((h) => h && h.target === 'model3d') : [];

          const mv = document.createElement('model-viewer');
          
          // Final safety check before setting src - ensure URL is absolutely correct
          const finalModelUrl = resolveMediaUrl(modelUrl);
          console.log('[3D Modal] Setting model-viewer src:', finalModelUrl);
          
          // Verify it's not still invalid
          if (finalModelUrl.includes('://assets/')) {
            console.error('[3D Modal] ERROR: Invalid URL detected!', finalModelUrl);
            const emergencyFix = finalModelUrl.replace(/https?:\/\/assets/, '/assets');
            mv.setAttribute('src', resolveMediaUrl(emergencyFix));
          } else {
            mv.setAttribute('src', finalModelUrl);
          }
          mv.setAttribute('alt', '3D model');
          mv.setAttribute('camera-controls', '');
          mv.setAttribute('interaction-prompt', 'auto');
          mv.style.width = '100%';
          mv.style.height = '500px';
          mv.style.background = '#F9F9F9';
          mv.style.borderRadius = '8px';

          if (opts && opts.auto_rotate) mv.setAttribute('auto-rotate', '');
          if (opts && opts.exposure) mv.setAttribute('exposure', String(opts.exposure));
          if (opts && opts.camera_orbit) mv.setAttribute('camera-orbit', String(opts.camera_orbit));

          container.innerHTML = '';
          container.style.position = 'relative';
          container.appendChild(mv);

          // Target-based visibility: model3d hotspots only inside 3D modal.
          if (modelHotspots.length) {
            const overlay = document.createElement('div');
            overlay.className = 'hotspots-overlay';
            overlay.style.zIndex = '20';
            modelHotspots.forEach((h) => overlay.appendChild(createHotspotDot(h)));
            container.appendChild(overlay);
          }
        })
        .catch(() => {
          showError(container, 'Failed to load 3D viewer.');
        });
    });

    modalEl.addEventListener('hidden.bs.modal', function () {
      const container = modalEl.querySelector('.js-media-3d');
      if (!container) return;
      // Remove model-viewer element to release GPU resources
      container.innerHTML = '';
    });
  }

  // -----------------------
  // 360 modal (simple frame scrubbing)
  // -----------------------
  const v360Cache = {
    manifestUrl: null,
    frames: null,
  };

  function fetchManifest(url) {
    const resolvedUrl = resolveMediaUrl(url);
    return fetch(resolvedUrl, { credentials: 'same-origin' }).then((r) => {
      if (!r.ok) throw new Error('Manifest fetch failed');
      return r.json();
    });
  }

  function preloadImage(url) {
    return new Promise((resolve, reject) => {
      const img = new Image();
      img.onload = () => resolve();
      img.onerror = () => reject();
      img.src = resolveMediaUrl(url);
    });
  }

  function initScrubViewer(container, frames) {
    const state = {
      frames,
      idx: 0,
      dragging: false,
      startX: 0,
      startIdx: 0,
      step: 8, // px per frame
      playing: false,
      playTimer: null,
      playSpeed: 180, // ms per frame (autoplay speed) - slower for smoother rotation
    };

    container.innerHTML = '';

    const img = document.createElement('img');
    img.alt = '360 view';
    img.style.width = '100%';
    img.style.height = 'auto';
    img.style.maxHeight = '500px';
    img.style.objectFit = 'contain';
    img.draggable = false;

    const hint = el(
      `<div class="text-muted small mt-2 text-center">Drag left/right to rotate</div>`
    );

    const wrap = document.createElement('div');
    wrap.style.display = 'flex';
    wrap.style.alignItems = 'center';
    wrap.style.justifyContent = 'center';
    wrap.style.minHeight = '500px';
    wrap.style.position = 'relative';
    wrap.appendChild(img);

    container.appendChild(wrap);
    container.appendChild(hint);

    // Frame hotspots overlay (target=frame360)
    const overlay = document.createElement('div');
    overlay.className = 'hotspots-overlay';
    overlay.style.zIndex = '20';
    wrap.appendChild(overlay);

    // -----------------------
    // Controls (prev / play-pause / next) - icon only
    // -----------------------
    function svgIcon(name) {
      if (name === 'prev') {
        return `
          <svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M12.5 4.5L7 10l5.5 5.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>`;
      }
      if (name === 'next') {
        return `
          <svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M7.5 4.5L13 10l-5.5 5.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>`;
      }
      if (name === 'pause') {
        return `
          <svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M7 5.5v9" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>
            <path d="M13 5.5v9" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>
          </svg>`;
      }
      // play
      return `
        <svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true">
          <path d="M8 6.5v7l6-3.5-6-3.5Z" fill="currentColor"/>
        </svg>`;
    }

    const controls = document.createElement('div');
    controls.setAttribute('role', 'group');
    controls.setAttribute('aria-label', '360° controls');
    controls.style.position = 'absolute';
    controls.style.left = '50%';
    controls.style.bottom = '14px';
    controls.style.transform = 'translateX(-50%)';
    controls.style.display = 'flex';
    controls.style.gap = '10px';
    controls.style.alignItems = 'center';
    controls.style.padding = '10px 12px';
    controls.style.borderRadius = '999px';
    controls.style.background = 'rgba(255,255,255,0.92)';
    controls.style.boxShadow = '0 6px 22px rgba(0,0,0,0.14)';
    controls.style.backdropFilter = 'blur(6px)';
    controls.style.zIndex = '30';

    function makeBtn(label, iconName) {
      const b = document.createElement('button');
      b.type = 'button';
      b.setAttribute('aria-label', label);
      b.style.width = '44px';
      b.style.height = '44px';
      b.style.display = 'inline-flex';
      b.style.alignItems = 'center';
      b.style.justifyContent = 'center';
      b.style.border = '1px solid rgba(0,0,0,0.08)';
      b.style.borderRadius = '999px';
      b.style.background = '#fff';
      b.style.color = '#111';
      b.style.cursor = 'pointer';
      b.style.transition = 'transform 0.08s ease, box-shadow 0.08s ease';
      b.innerHTML = svgIcon(iconName);
      b.addEventListener('mousedown', () => { b.style.transform = 'scale(0.98)'; });
      b.addEventListener('mouseup', () => { b.style.transform = ''; });
      b.addEventListener('mouseleave', () => { b.style.transform = ''; });
      return b;
    }

    const btnPrev = makeBtn('Previous frame', 'prev');
    const btnPlay = makeBtn('Play', 'play');
    const btnNext = makeBtn('Next frame', 'next');
    controls.appendChild(btnPrev);
    controls.appendChild(btnPlay);
    controls.appendChild(btnNext);
    wrap.appendChild(controls);

    function renderFrameHotspots() {
      const hsRaw = container.getAttribute('data-hotspots') || '[]';
      let hotspots = [];
      try { hotspots = JSON.parse(hsRaw) || []; } catch (_) { hotspots = []; }
      const frameHotspots = Array.isArray(hotspots) ? hotspots.filter((h) => h && h.target === 'frame360') : [];

      // Current frame is 1-based in admin
      const currentFrame = state.idx + 1;
      overlay.innerHTML = '';
      frameHotspots
        .filter((h) => typeof h.frame === 'number' && h.frame === currentFrame)
        .forEach((h) => overlay.appendChild(createHotspotDot(h)));
    }

    function render() {
      const frameUrl = state.frames[state.idx] || '';
      img.src = frameUrl ? resolveMediaUrl(frameUrl) : '';
      renderFrameHotspots();
    }

    function setIndex(next) {
      const n = state.frames.length;
      state.idx = ((next % n) + n) % n;
      render();
    }

    function stopAutoPlay() {
      state.playing = false;
      if (state.playTimer) {
        clearInterval(state.playTimer);
        state.playTimer = null;
      }
      btnPlay.setAttribute('aria-label', 'Play');
      btnPlay.innerHTML = svgIcon('play');
    }

    function startAutoPlay() {
      if (state.playing) return;
      state.playing = true;
      btnPlay.setAttribute('aria-label', 'Pause');
      btnPlay.innerHTML = svgIcon('pause');
      state.playTimer = setInterval(() => {
        // Avoid fighting with user drag
        if (state.dragging) return;
        setIndex(state.idx + 1);
      }, state.playSpeed);
    }

    function toggleAutoPlay() {
      if (state.playing) stopAutoPlay();
      else startAutoPlay();
    }

    btnPrev.addEventListener('click', () => {
      stopAutoPlay();
      setIndex(state.idx - 1);
    });
    btnNext.addEventListener('click', () => {
      stopAutoPlay();
      setIndex(state.idx + 1);
    });
    btnPlay.addEventListener('click', () => {
      toggleAutoPlay();
    });

    function onPointerDown(e) {
      state.dragging = true;
      state.startX = e.clientX;
      state.startIdx = state.idx;
      try { img.setPointerCapture(e.pointerId); } catch (_) {}
    }

    function onPointerMove(e) {
      if (!state.dragging) return;
      const dx = e.clientX - state.startX;
      const deltaFrames = Math.floor(dx / state.step);
      setIndex(state.startIdx + deltaFrames);
    }

    function onPointerUp() {
      state.dragging = false;
    }

    img.addEventListener('pointerdown', onPointerDown);
    img.addEventListener('pointermove', onPointerMove);
    img.addEventListener('pointerup', onPointerUp);
    img.addEventListener('pointercancel', onPointerUp);
    img.addEventListener('pointerleave', onPointerUp);

    // Initial render
    render();

    // Return cleanup
    return () => {
      stopAutoPlay();
      img.removeEventListener('pointerdown', onPointerDown);
      img.removeEventListener('pointermove', onPointerMove);
      img.removeEventListener('pointerup', onPointerUp);
      img.removeEventListener('pointercancel', onPointerUp);
      img.removeEventListener('pointerleave', onPointerUp);
    };
  }

  function init360Modal() {
    const modalEl = document.getElementById('modal360View');
    const bs = getBootstrap();
    if (!modalEl || !bs) return;

    let cleanup = null;

    modalEl.addEventListener('shown.bs.modal', function () {
      const container = modalEl.querySelector('.js-media-360');
      if (!container) return;
      const manifestUrlRaw = container.getAttribute('data-manifest-url') || '';
      if (!manifestUrlRaw) {
        showError(container, '360° manifest not available.');
        return;
      }

      // Resolve URL properly for production
      let manifestUrl = resolveMediaUrl(manifestUrlRaw);
      
      // Debug logging - always log to help diagnose issues
      console.log('[360 Modal] URL resolution:', { 
        raw: manifestUrlRaw, 
        resolved: manifestUrl,
        origin: window.location.origin 
      });
      
      // CRITICAL: Double-check - if still invalid, force fix
      if (manifestUrl.includes('://assets/')) {
        console.error('[360 Modal] CRITICAL: URL still invalid after resolution!', manifestUrl);
        // Force fix one more time
        const forcedFix = manifestUrl.replace(/https?:\/\/assets/, '/assets');
        manifestUrl = resolveMediaUrl(forcedFix);
        console.log('[360 Modal] Forced fix applied:', manifestUrl);
      }

      showLoading(container, 'Loading 360° view...');

      const useCache = v360Cache.manifestUrl === manifestUrlRaw && Array.isArray(v360Cache.frames) && v360Cache.frames.length;
      const p = useCache ? Promise.resolve({ frames: v360Cache.frames }) : fetchManifest(manifestUrl);

      p.then((manifest) => {
        const frames = Array.isArray(manifest.frames) ? manifest.frames : [];
        if (!frames.length) {
          showError(container, 'No 360° frames found.');
          return;
        }

        v360Cache.manifestUrl = manifestUrlRaw; // Cache using original URL for comparison
        const resolvedFrames = frames.map(frame => {
          // Ensure all frame URLs are also resolved properly
          if (typeof frame === 'string') {
            return resolveMediaUrl(frame);
          }
          return frame;
        });
        v360Cache.frames = resolvedFrames;

        // Preload first few frames for instant drag
        return preloadImage(resolvedFrames[0])
          .catch(() => null)
          .then(() => {
            cleanup = initScrubViewer(container, resolvedFrames);
          });
      }).catch(() => {
        showError(container, 'Failed to load 360° view.');
      });
    });

    modalEl.addEventListener('hidden.bs.modal', function () {
      const container = modalEl.querySelector('.js-media-360');
      if (cleanup) cleanup();
      cleanup = null;
      if (container) container.innerHTML = '';
    });
  }

  // -----------------------
  // DOM-level URL fix (runs before init)
  // Fixes invalid URLs like: https://assets/...
  // -----------------------
  function fixInvalidUrlsInDOM() {
    try {
      // 3D: data-model-url
      document.querySelectorAll('[data-model-url]').forEach((node) => {
        const raw = node.getAttribute('data-model-url');
        if (raw && /^https?:\/\/assets\//.test(raw)) {
          const fixed = raw.replace(/^https?:\/\/assets/, '/assets');
          node.setAttribute('data-model-url', fixed);
          console.log('[DOM Fix] data-model-url:', { from: raw, to: fixed });
        }
      });

      // 360: data-manifest-url
      document.querySelectorAll('[data-manifest-url]').forEach((node) => {
        const raw = node.getAttribute('data-manifest-url');
        if (raw && /^https?:\/\/assets\//.test(raw)) {
          const fixed = raw.replace(/^https?:\/\/assets/, '/assets');
          node.setAttribute('data-manifest-url', fixed);
          console.log('[DOM Fix] data-manifest-url:', { from: raw, to: fixed });
        }
      });
    } catch (_) {
      // no-op
    }
  }

  // -----------------------
  // Boot
  // -----------------------
  function boot() {
    // Fix invalid URLs immediately before initializing modals
    fixInvalidUrlsInDOM();
    init360Modal();
    init3DModal();
    initVideoModal();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();

