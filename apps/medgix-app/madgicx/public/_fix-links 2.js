document.addEventListener('DOMContentLoaded',()=>{
  const fix=h=>{if(!h)return h;try{h=decodeURIComponent(h);}catch(e){};h=h.replace(/index\.html%3F/gi,'?').replace(/index\.html\?/gi,'?');if(/^\?/.test(h))h='/'+h;return h;};
  document.querySelectorAll('a[href],form[action]').forEach(el=>{
    const k=el.tagName==='FORM'?'action':'href', v=el.getAttribute(k), nv=fix(v);
    if(nv && nv!==v) el.setAttribute(k,nv);
  });
});
