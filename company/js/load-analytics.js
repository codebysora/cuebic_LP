(function () {
  var loaded = false;

  function loadAnalytics() {
    if (loaded) return;
    loaded = true;

    var gtagScript = document.createElement('script');
    gtagScript.async = true;
    gtagScript.src = 'https://www.googletagmanager.com/gtag/js?id=AW-18142543562';
    document.head.appendChild(gtagScript);

    window.dataLayer = window.dataLayer || [];
    function gtag() {
      window.dataLayer.push(arguments);
    }
    window.gtag = gtag;
    gtag('js', new Date());
    gtag('config', 'AW-18142543562');

    if (window.__gtagConversion) {
      gtag('event', 'conversion', window.__gtagConversion);
    }

    (function (w, d, s, l, i) {
      w[l] = w[l] || [];
      w[l].push({ 'gtm.start': new Date().getTime(), event: 'gtm.js' });
      var f = d.getElementsByTagName(s)[0];
      var j = d.createElement(s);
      var dl = l !== 'dataLayer' ? '&l=' + l : '';
      j.async = true;
      j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
      f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-NBB3LPJT');
  }

  function schedule() {
    if ('requestIdleCallback' in window) {
      requestIdleCallback(loadAnalytics, { timeout: 3500 });
    } else {
      setTimeout(loadAnalytics, 2000);
    }
  }

  if (window.__loadAnalyticsImmediate) {
    loadAnalytics();
  } else if (document.readyState === 'complete') {
    schedule();
  } else {
    window.addEventListener('load', schedule);
  }
})();
