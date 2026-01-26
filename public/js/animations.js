(function(){
  'use strict';

  function onReady(fn){
    if (document.readyState !== 'loading') return fn();
    document.addEventListener('DOMContentLoaded', fn);
  }

  function revealInit(){
    var observerOptions = { root: null, rootMargin: '0px', threshold: 0.12 };
    var observer = new IntersectionObserver(function(entries){
      entries.forEach(function(entry){
        if (entry.isIntersecting) {
          var el = entry.target;
          var anim = el.dataset.anim || 'anim-fade-up';
          el.classList.add(anim);
          el.classList.add('did-animate');
          observer.unobserve(el);
        }
      });
    }, observerOptions);

    document.querySelectorAll('.will-animate').forEach(function(el){
      observer.observe(el);
    });
  }

  function attachHoverHelpers(){
    // Add small keyboard-friendly focus handling
    document.querySelectorAll('.button, a, button, .hover-lift').forEach(function(el){
      el.addEventListener('focus', function(){ el.classList.add('focus-anim'); });
      el.addEventListener('blur',  function(){ el.classList.remove('focus-anim'); });
    });
  }

  onReady(function(){
    revealInit();
    attachHoverHelpers();
  });

})();
