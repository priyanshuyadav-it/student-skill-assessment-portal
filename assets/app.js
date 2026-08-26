document.addEventListener('DOMContentLoaded',()=>{document.querySelectorAll('[data-autohide]').forEach(e=>setTimeout(()=>e.remove(),4000));});
function confirmSubmit(){return confirm('Submit this assessment now?');}
function startTimer(seconds,elementId,formId){
 let left=seconds, el=document.getElementById(elementId);
 const timer=setInterval(()=>{let m=Math.floor(left/60),s=left%60;el.textContent=`${m}:${String(s).padStart(2,'0')}`;
 if(left<=0){clearInterval(timer);document.getElementById(formId).submit();} left--;},1000);
}